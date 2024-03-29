<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $manager;

    #[Route(path: '/compte/adresses', name: 'account_address')]
    public function index(): Response
    {
        //dump($this->getUser()->getAddresses());
        return $this->render('account/address.html.twig', [
            'controller_name' => 'AccountAddressController',
        ]);
    }


    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route(path: '/compte/ajouter-une-adresse', name: 'account_address_add')]
    public function add(#[CurrentUser] ?User $user, Request $request, Cart $cart): Response
    {

        // si il y a des produits dans le panier , on va vers order (/commande)         
        if ($cart->get() && $user->getAddresses()->getValues()) {
            return $this->redirectToRoute('order');
        } // sinon on va vers la gestion des adresses 
        else {
            $this->redirectToRoute('account_address');
        }

        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser());
            $this->manager->persist($address);
            $this->manager->flush();
            $this->addFlash(
                'success',
                "L'address {$address->getName()} a bien été créé"
            );
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/compte/modifier-une-adresse/{id}', name: 'account_address_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Address $address): Response
    {
        // on vérifie qu'une adresse existe ou qu'elle appartient à l'utilisateur connecté 
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //$manager->persist($address); 
            // previent doctrine que l'on veut sauver on persiste dans le temps 
            $manager->flush(); // envoi la requête à la base de donnée 
            $this->addFlash('success', "L'adresse {$address->getName()} a bien été modifiée"); // on retourne vers l'accueil return 
            $this->redirectToRoute('account_address');
        }
        return $this->render('account/address_add.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route(path: '/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete(EntityManagerInterface $manager, Address $address): Response
    {
        // on vérifie qu'une adresse existe ou qu'elle appartient à l'utilisateur connecté 
        if ($address->getUser() == $this->getUser()) {
            $manager->remove($address);
            $manager->flush(); // envoyer l'info à la bdd 
            $this->addFlash(
                'success',
                "L'adresse {$address->getName()} a bien été supprimée"
            );
            return $this->redirectToRoute('account_address');
        } else {
            $this->addFlash(
                'Violation',
                "Vous essayé de supprimé une adresse qui ne vous appartient pas ! espèce d'enfoiré"
            );
        }
        return $this->redirectToRoute('account_address');
        
    }
}
