<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if (!$user->getAddresses()->getValues()) { 
            return $this->redirectToRoute('account_address_add');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            # code...
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
