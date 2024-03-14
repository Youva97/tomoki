<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchFilters;
use App\Form\SearchFiltersType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/nos-produits', name: 'products')]
    public function index(ProductRepository $repo, Request $request): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);

        $search = new SearchFilters();
        $form = $this->createForm(SearchFiltersType::class, $search);
        $form->handleRequest($request);
        dump($search);

        if ($form->isSubmitted() && $form->isValid()) { // on récupère les catégories sélectionnées 
            $categorie_selectionnee = $search->getCategories(); //dump($categorie_selectionnee); // si des categories ont été selectionnées 
            if (count($categorie_selectionnee) > 0) { //on récupère tous les ids des catégories que l'on met dans un tableau 
                foreach ($categorie_selectionnee as $categorie) { 
                    $id_cat[] = $categorie->getId(); //dump($categorie); 
                } 
                //dump($id_cat); // $products = $repo->findBy(['category' => ['410', '413']]); 
                $products = $repo->findBy(['category' => $id_cat]); if (!$products) 
                { 
                    $error = "Il n'y a pas de produits disponibles"; 
                } else $error = null; //dump($products); 
            } else {
                 if ($products = $repo->findAll()) //recupère tous les enregistrements de la table visée 
                 $error = null; else $error = "Il n'y a pas de produits disponibles"; 
                } 
            } else { if ($products = $repo->findAll()) //recupère tous les enregistrements de la table visée 
                $error = null; else $error = "Il n'y a pas de produits disponibles"; 
            } 
            return $this->render('product/index.html.twig', [ 
                'products' => $products, 
                "form" => $form->createView(), 
                'error' => $error, 
            ]); 
    }

    #[Route('/nos-produits/{id}', name: 'products')]
    public function show (Product $product){
        return $this->render('product/show.html.twig', [ 'product' => $product ]);
    } 

}
