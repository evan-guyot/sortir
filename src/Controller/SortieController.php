<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AjoutSortieType;
use App\Form\SiteType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'app_sortie_create')]
    public function index(Request $request, ParticipantRepository $participantRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $participant = $participantRepository->find(1);

        if ($participant == null) {
            return $this->redirectToRoute('app_main');
        }

        $sortie = new Sortie();

        $siteForm = $this->createForm(SiteType::class, $participant->getSite());

        $sortie->setOrganisateur($participant);
        $sortieForm = $this->createForm(AjoutSortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted()) {
            if ($sortieForm->isValid()) {
                $etatValue = $sortieForm->getClickedButton()->getName() === 'publier' ? 'Ouvert' : 'En création';

                $etat = $etatRepository->getOrMakeEtat($etatValue, $entityManager);
                $sortie->setEtat($etat);
                $entityManager->persist($sortie->getLieu());
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash("success", "Votre sortie à bien été enregistrée");

                return $this->redirectToRoute('app_sortie_create');
            } else {
                $this->addFlash("error", "Merci de remplir correctement tous les champs");
            }
        }

        return $this->render('sortie/create.html.twig', [
            'controller_name' => 'SortieController',
            'sortie_form' => $sortieForm,
            'site_form' => $siteForm
        ]);
    }
}
