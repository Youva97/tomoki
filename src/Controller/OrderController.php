<?php

namespace App\Controller;


use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Order;
use App\Services\Cart;
use App\Form\OrderType;
use App\Entity\OrderDetails;

use Stripe\Checkout\Session;
use App\Repository\ProductRepository;
use App\Services\stripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $manager, Cart $cart, ProductRepository $repo): Response
    {



        if (!$user->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }


        $cart = $cart->get();
        $cartComplete = [];
        foreach ($cart as $id => $quantity) {
            $cartComplete[] = [
                'product' => $repo->findOneById($id),
                'quantity' => $quantity,
            ];
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);


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
/*             $product_for_stripe = []; */
            foreach ($cartComplete as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']);
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice()); //dump($product); 

/*                 $product_for_stripe[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product['product']->getName(),
                            'images' => [
                                [$_SERVER['HTTP_ORIGIN'] . '/uploads/' . $product['product']->getIllustration()]
                            ],
                        ],
                        'unit_amount' => $product['product']->getPrice(), // Prix en centimes (converti en centimes)
                    ],
                    'quantity' => $product['quantity'],
                ]; */
                $manager->persist($orderDetails);
            }

/*             $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $order->getCarrier()->getName(),
                    ],
                    'unit_amount' => $order->getCarrier()->getPrice(),
                ],
                'quantity' => 1,
            ];
            $stripeSecretKey = $this->getParameter('STRIPE_KEY');
            Stripe::setApiKey($stripeSecretKey);
            $YOUR_DOMAIN = 'http://localhost:8000';
            $checkout_session = Session::create([
                'line_items' => $product_for_stripe,
                'customer_email' => $this->getUser()->getEmail(),
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/commande/dommage/{CHECKOUT_SESSION_ID}'
            ]);
            $order->setStripeSessionId($checkout_session->id);
            dump($checkout_session->url); */

            $manager->flush();

            return $this->render('order/recap.html.twig', [
                'cart' => $cartComplete,
                'order' => $order,
                'stripe_checkout_session' => $checkout_session->url,
            ]);
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartComplete,
        ]);
    }

}
