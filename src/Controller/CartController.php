<?php

namespace App\Controller;

use App\Services\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    #[Route(path: '/mon-panier', name: 'cart')]
    public function index(Cart $cart, ProductRepository $product): Response
    { //dump($cart->get()); 
        $cart = $cart->get();
        $cartComplete = [];
        foreach ($cart as $id => $quantity) {
            $cartComplete[] = [
                'product' => $product->findOneById($id),
                'quantity' => $quantity
            ];
        }
        //dump($cartComplete); 
        return $this->render('cart/index.html.twig', [
            'cart' => $cartComplete
        ]);
    }

    #[Route(path: '/cart/add/{id}', name: 'add_to_cart')]
    public function add($id, Cart $cart): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    #[Route(path: '/cart/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('cart');
    }

    #[Route(path: '/cart/delete/{id}', name: 'delete_to_cart')]
    public function delete(Cart $cart, $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }

    #[Route(path: '/cart/decrease/{id}', name: 'decrease_to_cart')] public function decrease(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('cart');
    }
}
