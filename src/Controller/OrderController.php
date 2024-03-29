<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $manager, Cart $cart, ProductRepository $repo): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        $cart = $cart->get();
        $cartComplete = [];
        foreach ($cart as $id => $quantity) {
            $cartComplete[] = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity,
            ];
        }

        if (!$user->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setCarrier($form->get('transporteurs')->getData());
            $order->setDelivery($form->get('addresses')->getData());
            $order->setStatut(0);
            $date = new \DateTime();
            $date = $date->format('dmY');
            $order->setReference($date . '-' . uniqid());
            $manager->persist($order); // Enregistrer mes produit OrderDetails 
            //dump($cartComplete); 
            foreach ($cartComplete as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']);
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice()); //dump($product); 
                $manager->persist($orderDetails);
            }
            return $this->render('order/recap.html.twig', [
                'cart' => $cartComplete,
                'order' => $order,
            ]);
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartComplete,
        ]);
    }
}
