<?php


namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;



class Cart
{

    private $requestStack;
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function add($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else $cart[$id] = 1; // on ajoute une quantité 1 pour l'id $id 
        $this->requestStack->getSession()->set('cart', $cart); //dd($cart)
    }
    public function get()
    {
        return $this->requestStack->getSession()->get('cart', []);
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    public function delete($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        unset($cart[$id]);
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        if (isset($cart[$id]) && $cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }
}
