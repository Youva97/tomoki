<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccountAddressController extends AbstractController
{
    #[Route(path: '/compte/ajouter-une-adresse', name: 'account_address_add')] public function add(): Response
    {
        return $this->render('account/address_add.html.twig', ['controller_name' => 'AccountAddressController',]);
    }
}
