<?php

namespace App\Controller;

use App\Form\ModifierMonMotDePasseType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ModifierMonMotDePasseController extends AbstractController
{
    // Injecte l'EntityManager et le passwordHasher dans le contrôleur
    private $passwordHasher;
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    #[Route('/modificationmotdepasse', name: 'modifier_mon_mot_de_passe')]
    public function modifierMonMotDePasse(Request $request, UserPasswordHasherInterface $passwordHasher, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($this->getUser());

        $mdpInitial = $participant->getMotdepasse();

        $mdpForm = $this->createForm(ModifierMonMotDePasseType::class, $participant);

        $mdpForm->handleRequest($request);

        if ($mdpForm->isSubmitted()) {
            if ($mdpForm->isValid()) {
                if (strlen($participant->getPassword()) >= 6) {
                    $participant->setMotdepasse(
                        $passwordHasher->hashPassword(
                            $participant,
                            $participant->getPassword()
                        )
                    );
                    $this->entityManager->flush();
                    return $this->redirectToRoute('gestion_mon_profil');
                } else {
                    $participant->setMotdepasse($mdpInitial);
                    $this->addFlash("error", "Le mot de passe doit contenir au minimum 6 caractères.");
                    return $this->redirectToRoute('modifier_mon_mot_de_passe');
                }
            } else {
                $this->addFlash("error", "Les mots de passes doivent correspondre entre eux.");
                return $this->redirectToRoute('modifier_mon_mot_de_passe');
            }
        }

        return $this->render('modifier_mon_mot_de_passe/index.html.twig', [
            'mdpForm' => $mdpForm->createView(),
            'controller_name' => 'ModifierMonMotDePasseController',
        ]);
    }
}