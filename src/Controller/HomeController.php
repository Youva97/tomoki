<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(RequestStack $requestStack): Response {
        // dump($requestStack->getSession()->get('cart')); 
        $panier = $requestStack->getSession()->get('cart', []); // si le panier est vide on renvoit un tableau vide 
        // dump($panier); 
        $panier[12] = 34; 
        $requestStack->getSession('cart', $panier); 
        // dump($panier); 
        $panier[7] = 6; 
        $requestStack->getSession()->set('cart', $panier); 
        // dump($panier); 
        $requestStack->getSession()->remove('cart'); 
        // dump($requestStack->getSession()->get('cart')); 
        return $this->render('home/index.html.twig'); 
    }

}
