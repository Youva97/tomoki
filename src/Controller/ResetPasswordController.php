<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{

    private $passwordHasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $manager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->manager = $manager;
    }
    #[Route(path: '/mot-de-passe-oublie', name: 'reset_password')]
    public function index(Request $request, UserRepository $repo): Response
    {
        if ($this->getUser()) {

            return $this->redirectToRoute('account');
        }

        if ($request->get('email')) {

            $user = $repo->findOneByEmail($request->get('email'));
            if ($user) {

                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new \DateTime());

                $this->manager->persist($resetPassword);
                $this->manager->flush();

                //generer une route

                $url = $this->generateUrl('update_password', ['token' => $resetPassword->getToken()]);

                $contentEmail = 'Réinitialisation du mail, cliquez sur le lien ci-dessous<br>
                <a href="' . $_SERVER['HTTP_ORIGIN'] . $url . '">Reinitialisation du mot de passe</a>';

                mail($user->getEmail(), 'Reinitialisation mdp', $contentEmail);

                $this->addFlash(
                    'success',
                    'Vous allez recevoir un email avec la procedure de reinitialisation'
                );
                return $this->redirectToRoute('home');
            } else {

                $this->addFlash(
                    'danger',
                    'L\'email ' . $request->get('email') . 'n\'existe pas, veuillez creer un compte'
                );

                return $this->redirectToRoute('register');
            }
        }

        return $this->render('reset_password/index.html.twig', [
            'controller_name' => 'ResetPasswordController',
        ]);
    }

    #[Route(path: '/modifier-mon-mot-de-passe/{token}', name: 'update_password')]
    public function update($token, ResetPasswordRepository $repo, Request $request): Response
    {
        $reset_password = $repo->findOneByToken($token);
        if (!$reset_password) {
            $this->addFlash(
                'danger',
                'L\'URL est incorrect'
            );
            return $this->redirectToRoute('home');
        } // verifier que now < createdAt + 3h par exemple 
        $date_create = $reset_password->getCreatedAt(); //dump($date_create); //dump($date_create->modify('+ 3 hour')); //dump($date_create->modify('+ 10 minutes')); 
        $now = new \DateTime();
        if ($now > $date_create->modify('+ 1 hour')) {
            $this->addFlash('danger', "Votre demande de modification de mot de passe a expiré");
            return $this->redirectToRoute('reset_password');
        } // rendre une vue avec mot de passe et confirmation 

        // rendre une vue avec mot de passe et confirmation 
        $user = $reset_password->getUser(); // (voir entité ResetPassword) 
        $form = $this->createForm(ResetPasswordType::class, $user); // dd($form->getData()); 
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $user->getNewPassword()
            ));
            $this->manager->persist($user); // previent doctrine que l'on veut sauver on persiste dans le temps 
            $this->manager->flush(); // envoi la requête à la base de donnée 
            $this->addFlash('success', "Le nouveau mot de passe a bien été créé");
            return $this->redirectToRoute('connexion');
        }
        return $this->render('reset_password/update.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}
