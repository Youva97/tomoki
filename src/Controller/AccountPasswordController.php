<?php

namespace App\Controller;

use App\Form\ChangePasswordType;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AccountPasswordController extends AbstractController
{
    #[Route(path: '/compte/modifier-mon_mot-de-passe', name: 'account_password')]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // recupérer l'utilisateur qui est connecté
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);
        // on relie les champs du formulaire aux champs de l'utilisateur
        $form->handleRequest($request);
        dump($user);
        // équivalent de var_dump

        if ($form->isSubmitted() && $form->isValid()) {
            // https://symfony.com/doc/current/components/security/authentication.html
            if (!$passwordHasher->isPasswordValid($user, $user->getOldPassword()))
            //if (!password_verify($user->getOldPassword(), $user->getPassword()))
            {
                $this->addFlash('danger', "L'ancien mot de passe est incorrect");
            } else {
                $newPassword = $user->getNewPassword();
                $pass_encoded = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($pass_encoded);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', "Votre mot de passe a bien été modifié");
                return $this->redirectToRoute('account');
            }
        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(), // envoi du form à twig
        ]);
    }
}
