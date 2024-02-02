<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new Participant();


        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_USER"]);
            $user->setMotdepasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $user->getMotdepasse()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Vous avez été enregistré");

            return $this->redirectToRoute("app_login");
        }

        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
    }
}
