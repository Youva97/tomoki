<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/nos-produits', name: 'products')]
    public function index(ProductRepository $repo): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Product::class);

        $products = $repo->findAll();
        dump($products);

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

/*     #[Route('/nos-produits/{id}', name: 'products')]
    public function show (Product $product){
        return $this->render('product/show.html.twig', [ 'product' => $product ]);
    } */

}
