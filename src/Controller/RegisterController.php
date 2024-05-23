<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $passwordHasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $manager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->manager = $manager;
    }

    #[Route(path: '/inscription', name: 'register')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            ));

            $this->manager->persist($user);
            $this->manager->flush();

            $contentEmail = "Bonjour" . $user->getEmail() . "<br
            Merci pour votre inscription, le compte a été créer et doit être activé via le lien d'activation ci-dessous <br>
            http://lien";

            mail($user->getEmail(), 'Activation de compte', $contentEmail);



            $this->addFlash(
                'success',
                'Le compte ' . $user->getEmail() . ' a bien été créé et doit être activé, un mail vous à été envoyé'
            );

            return $this->redirectToRoute('connexion'); // Redirection vers la page de connexion
        }

        return $this->render('register/index.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    #[Route(path: '/inscription/{email}/token', name: 'register_active')]
    public function register_active(User $user, $token)
    {
        $token_verif = sha1($user->getEmail() . $user->getPassword());
        if (!$user->isActive()) {
            if ($token == $token_verif) {
                $user->setActive(true);
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    'Votre compte est activé avec success'
                );
                return $this->redirectToRoute('connexion');
            } else {
                $this->addFlash(
                    'danger',
                    'Lien d\'activation incorrect'
                );
                return $this->redirectToRoute('home');
            }
        } else {
            $this->addFlash(
                'success',
                'Compte déjà activé'
            );
            return $this->redirectToRoute('home');
        }
        return $this->redirectToRoute('connexion');
    }
}
