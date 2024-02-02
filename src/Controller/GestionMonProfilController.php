<?php

namespace App\Controller;

use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\GestionMonProfilType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Site;

class GestionMonProfilController extends AbstractController
{
    // Injecte l'EntityManager dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/monprofil', name: 'gestion_mon_profil')]
    public function gestionProfil(Request $request, EntityManagerInterface $entityManager)
    {
        // POUR TESTER A ENLEVER
        //$userId = 3; // ID de l'utilisateur
        //$participant = $this->entityManager->getRepository(Participant::class)->find($userId);

        $user = $this->getUser();

        // Récupère les informations du participant depuis la base de données
        $participant = $this->entityManager->getRepository(Participant::class)->find($user->getId());

        $profilForm = $this->createForm(GestionMonProfilType::class, $participant);

        $siteRepository = $entityManager->getRepository(Site::class);
        $sites = $siteRepository->findAll();

        $pseudoInitial = $participant->getPseudo();

        $profilForm->handleRequest($request);// Gère la soumission du formulaire

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {

            // Vérifier si le pseudo a été modifié et si il est déja existant
            $pseudoActuel = $participant->getPseudo();
            if ($pseudoActuel !== $pseudoInitial) {
                $existingParticipant = $entityManager->getRepository(Participant::class)->findOneBy(['pseudo' => $pseudoActuel]);
                if ($existingParticipant) {
                    $errorMessage = 'Le pseudo saisi est déjà utilisé. Veuillez en choisir un autre.';
                    return $this->render('gestion_mon_profil/index.html.twig', [
                        'profilForm' => $profilForm->createView(),
                        'site' => $sites,
                        'error_message' => $errorMessage,
                    ]);
                }
            }
            $this->entityManager->persist($participant);
            $this->entityManager->flush();// Enregistre les modifications dans la base de données
            return $this->redirectToRoute('gestion_mon_profil');
        }

        // Afficher le formulaire
        return $this->render('gestion_mon_profil/index.html.twig', [
            'profilForm' => $profilForm->createView(),
            'site' => $sites,
            'error_message' => null,
        ]);
    }
}