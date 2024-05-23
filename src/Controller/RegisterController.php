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
    public function index(Request $request, $token): Response
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

            $token = sha1($user->getEmail() . $user->getPassword());

            $content_mail = 'Bonjour' . $user->getFirstName() . '' . $user->getLastName() . ',<br><br>
            Merci de vous etre inscrit sur Tomoki. Votre compte a été céé et doit etre activé avant que vous puissiez l\'utiliser.<br>
            Pour l\'activer,cliquez sur le lien ci dessous ou copiez et collez le dans votre navigateur:<br><a href="https://' . $_SERVER['HTTP_HOST'] . '/inscription/' . $user->getEmail() . '/' . $token . '" style="color: #5cff00">https://'
                . $_SERVER['HTTP_HOST'] . '/inscription/' . $user->getEmail() . '/' . $token . '</a><br><br>
            Apres activation vous pourrez vous connecter a < href="https://www.myboutique.com/" style="color:
            #5cff00">https://www.myboutique.com/</a> en utilisant l\'identifiant et le mot de passe suivants: <br>
            Identifiant:' . $user->getEmail() . '<br>
            Mot de passe:' . $user->getPassword();

            $mail->send($user->getEmail() . $user->getFirstName() . '' . $user->getLastName() . 'Details du compte utilisateur de' . $user->getFirstName() . '' . $user->getLastName() . 'sur Tomoki' . $content_mail);



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
                return $this->redirectToRoute('connexion');
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
