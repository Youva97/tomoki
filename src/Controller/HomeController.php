<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use App\Repository\OrderRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home')]
    public function index(RequestStack $requestStack, OrderRepository $repo): Response
    {
        /*      MailerInterface $mailer
   $email = (new Email())
            ->from('mineas.gael@outlook.com')
            ->to('mineas.gael@outlook.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);
        dd($mailer); */

        // dd($repo->findOrder('gaelmineas971@gmail.com'));
        // dump($requestStack->getSession()->get('cart')); 
        /*         $panier = $requestStack->getSession()->get('cart', []); // si le panier est vide on renvoit un tableau vide 
        // dump($panier); 
        $panier[12] = 34;
        $requestStack->getSession('cart', $panier);
        // dump($panier); 
        $panier[7] = 6;
        $requestStack->getSession()->set('cart', $panier);
        // dump($panier); 
        $requestStack->getSession()->remove('cart');
        // dump($requestStack->getSession()->get('cart')); */
        return $this->render('home/index.html.twig');
    }
}
