<?php

namespace App\Controller;

use App\Entity\Order;
use App\Services\Cart;
use Stripe\StripeClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    #[Route(path: '/commande/merci/{stripeSessionId}', name: 'order_success')]
    public function index(Order $order, EntityManagerInterface $manager, Cart $cart, $stripeSessionId): Response
    {

        if (!$order || $order->getUser() != $this->getUser()) return $this->redirectToRoute('home');
        dd($order);
        // on recupère la session pour vérifier que le paiement est bien effectuer 
        $stripe = new StripeClient($this->getParameter('STRIPE_KEY'));
        $session = $stripe->checkout->sessions->retrieve($stripeSessionId);

        dump($session);
        dd($session->payment_status); //si la commande n'est pas payée

        if ($session->payment_status != "paid")
            return $this->redirectToRoute('order_cancel', ['stripeSessionId' => $stripeSessionId]); // modifier statut 
        if (!$order->getStatut()) { // vider la session cart (le panier) 
            $cart->remove();
            $order->setStatut(1);
            $manager->flush();
        } // envoyer un email 
        return $this->render('order_success/index.html.twig', ['total' => $session->amount_total]);
    }
}
