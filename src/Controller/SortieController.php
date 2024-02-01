<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AjoutSortieType;
use App\Form\SiteType;
use App\Repository\EtatRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class SortieController extends AbstractController
{
    #[Route('', name: 'app_sortie')]
    public function index(Request $request, SiteRepository $siteRepository, SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {
        $user = $participantRepository->find(5);
        $today = time();
        $sites = $siteRepository->findAll();

        $cb1 = $request->query->get('cb1', false);
        $cb2 = $request->query->get('cb2', false);
        $cb3 = $request->query->get('cb3', false);
        $cb4 = $request->query->get('cb4', false);
        $site = $request->query->get('site', 'All');
        $motclef = $request->query->get('motclef', '');
        $dateDebut = $request->query->get('dateDebut', null);
        $dateFin = $request->query->get('dateFin', null);

        $sorties = $sortieRepository->findWithFilters($user, $cb1, $cb2, $cb3, $cb4, $site,$motclef, $dateDebut, $dateFin);

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sites' => $sites,
            'sorties' => $sorties,
            'today' => $today,
            'user' => $user,
        ]);
    }
  
    #[Route('/sortie/create', name: 'app_sortie_create')]
        public function create(Request $request, ParticipantRepository $participantRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
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

                    return $this->redirectToRoute('app_sortie');
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
