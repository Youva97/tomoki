<?php 


namespace App\Services; 
use Symfony\Component\HttpFoundation\RequestStack; 



class Cart { 

    private $requestStack; 
    public function __construct(RequestStack $requestStack) { 
        $this->requestStack = $requestStack; 
    } 
    public function add($id) { 
        $cart = $this->requestStack->getSession()->get('cart', []); 
        $cart[$id] = 1; // on ajoute une quantité 1 pour l'id $id 
        $this->requestStack->getSession()->set('cart', $cart); 
        //dd($cart); 
    } 
        public function get() { 
            return $this->requestStack->getSession()->get('cart', []); 
        } 
    }