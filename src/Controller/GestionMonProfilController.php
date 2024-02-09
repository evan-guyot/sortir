<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
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
    public function gestionProfil(Request $request, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository)
    {
        $errorMessage = "";
        $participant = $participantRepository->find($this->getUser());

        $profilForm = $this->createForm(GestionMonProfilType::class, $participant);

        $siteRepository = $entityManager->getRepository(Site::class);
        $sites = $siteRepository->findAll();

        $pseudoInitial = $participant->getPseudo();

        $profilForm->handleRequest($request);// Gère la soumission du formulaire

        if ($profilForm->isSubmitted() && $profilForm->isValid()) {

            $imageFile = $profilForm->get('maphoto')->getData();

            if ($imageFile) {
                $imageEntity = new Image();

                $imageName = $imageFile->getClientOriginalName();

                if (strlen($imageName) > 50) {
                    $this->addFlash("error", "Le nom de l\'image est trop long, 50 caractères maximum. Veuillez la renommer ou en choisir une autre.");
                    return $this->redirectToRoute('gestion_mon_profil');
                }

                $imageBase64 = base64_encode(file_get_contents($imageFile->getPathname()));

                $imageEntity->setNom($imageName);
                $imageEntity->setContenu($imageBase64);

                // Enregistrez l'entité Image dans la base de données
                $this->entityManager->persist($imageEntity);
                $this->entityManager->flush();

                $participant->setImage($imageEntity);
            }

            $pseudoActuel = $participant->getPseudo();
            if ($pseudoActuel !== $pseudoInitial) {
                $existingParticipant = $entityManager->getRepository(Participant::class)->findOneBy(['pseudo' => $pseudoActuel]);
                if ($existingParticipant) {
//                    $errorMessage .= 'Le pseudo saisi est déjà utilisé. Veuillez en choisir un autre.';
//                    return $this->redirectToRoute('gestion_mon_profil');
                    $participant->setPseudo($pseudoInitial);
                    $this->addFlash("error", "Le pseudo saisi est déjà utilisé. Veuillez en choisir un autre.");
                    return $this->redirectToRoute('gestion_mon_profil');
                }
            }
            $this->entityManager->persist($participant);
            $this->entityManager->flush();// Enregistre les modifications dans la base de données
            $this->addFlash("success", "Vos modifications ont bien été enregistrées");
            return $this->redirectToRoute('gestion_mon_profil');
        }

        return $this->render('gestion_mon_profil/index.html.twig', [
            'profilForm' => $profilForm->createView(),
            'site' => $sites,
//            'error_message' => $errorMessage,
            'participant' => $participant
        ]);
    }
}