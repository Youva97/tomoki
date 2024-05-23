<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\SearchFilters;
use App\Form\CommentType;
use App\Form\SearchFiltersType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route(path: '/nos-produits', name: 'products')]
    public function index(ProductRepository $repo, Request $request, PaginatorInterface $paginator): Response
    {

        $search = new SearchFilters();
        $form = $this->createForm(SearchFiltersType::class, $search);
        $form->handleRequest($request);
        $page = 1; // numéro de la page 
        $limit = 100; // on veut 100 enregistrements 
        $offset = $page * $limit - $limit; // 1 * 10 -10 = 0 // 2 * 10 -10 = 10 
        $products = $repo->findAll($offset, $limit);
        $total = count($products); // nombres d'enregistrements totals 
        $pages = ceil($total / $limit); // nombres de pages totales

        if ($form->isSubmitted() && $form->isValid()) {

            if (count($search->getCategories()) > 0 || $search->getString()) {
                //on récupère tous les ids des catégories que l'on met dans un tableau 

                /*                 foreach ($categorie_selectionnee as $categorie) {
                    $id_tab[] = $categorie->getId(); 
                } */

                $products = $repo->findWithSearch($search);
                $donnees = $repo->findBy(['category' => $search]);

                $products = $paginator->paginate(
                    $donnees, //on passe les données // $request->query->getInt('page', 1), /*page number*/ 
                    $page, // page sur laquelle on se trouve 
                    10 /*limit per page*/
                );
                /* $products = $repo->findBy(['category' => $id_tab]);  */

                if (!$products) {
                    $error = "Il n'y a pas de produits disponibles";
                } else $error = null; //dump($products); 

            } else {
                if ($products = $repo->findAll()) //recupère tous les enregistrements de la table visée 
                    $error = null;
                else $error = "Il n'y a pas de produits disponibles";
            }
        } else {
            if ($products = $repo->findAll()) //recupère tous les enregistrements de la table visée 
                $error = null;
            else $error = "Il n'y a pas de produits disponibles";
        }

        return
            $this->render('product/index.html.twig', [
                'products' => $products,
                "form" => $form->createView(),
                'error' => $error,
            ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function product(Product $product, CommentRepository $commentRepo)
    {
        $approvedComments  = $commentRepo->findAllComment(true);


        return $this->render('product/show.html.twig', [
            'product' => $product,
            'comments' => $approvedComments
        ]);
    }

    #[Route("/compte/mes-commandes/{slug}/comment", name: "comment_product")]
    public function comment(Product $product, Request $request, EntityManagerInterface $manager): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setUser($this->getUser());
            $comment->setProduct($product);
            $comment->setStatus(false);
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le commentaire pour le produit' . $product->getName() . 'a bien été enregistrer!'
            );

            return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
        }

        return $this->render('product/comment.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
}
