<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountOrderController extends AbstractController
{

    #[Route(path: '/compte/mes-commandes', name: 'account_order')]
    public function index(OrderRepository $repo): Response
    {
        //dd($repo->findSuccessOrder($this->getUser()));
        return $this->render('account/order.html.twig', [
            'orders' => $repo->findSuccessOrder($this->getUser())
        ]);
    }

    #[Route(path: '/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show(Order $order): Response
    {
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_order');
        }
        return $this->render('order/order_show.html.twig', [
            'order' => $order
        ]);
    }
}
