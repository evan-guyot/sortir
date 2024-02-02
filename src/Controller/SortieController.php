<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\AjoutSortieType;
use App\Form\SiteType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
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
    public function index(Request $request, EntityManagerInterface $entityManager, SiteRepository $siteRepository, SortieRepository $sortieRepository, ParticipantRepository $participantRepository, InscriptionRepository $inscriptionRepository, EtatRepository $etatRepository): Response
    {
        $user = $participantRepository->find(5);
        $today = new \DateTime();;
        $sites = $siteRepository->findAll();

        $cb1 = $request->query->get('cb1', false);
        $cb2 = $request->query->get('cb2', false);
        $cb3 = $request->query->get('cb3', false);
        $cb4 = $request->query->get('cb4', false);
        $site = $request->query->get('site', 'All');
        $motclef = $request->query->get('motclef', '');
        $dateDebut = $request->query->get('dateDebut', null);
        $dateFin = $request->query->get('dateFin', null);

        if ($request->isMethod('POST')) {
            $sortieidinscription = $request->request->get('sinscrireid');
            $sortieiddesister = $request->request->get('desisterid');

            if ($sortieiddesister !== null) {

                $inscription = $inscriptionRepository->findOneBy([
                    'sortie' => $sortieiddesister,
                    'participant' => $user->getId(),
                ]);

                if (!$inscription) {
                    throw new NotFoundHttpException('Inscription non trouvée.');
                }

                $entityManager->remove($inscription);
                $entityManager->flush();

            }


            if ($sortieidinscription !== null) {
                $inscription = new Inscription();
                $sortie = $sortieRepository->find($sortieidinscription);
                $now = new \DateTime();

                $inscription->setSortie($sortie);
                $inscription->setParticipant($user);
                $inscription->setDateInscription($now);

                $entityManager->persist($inscription);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_sortie');
        }

        $sorties = $sortieRepository->findWithFilters($user, $cb1, $cb2, $cb3, $cb4, $site, $motclef, $dateDebut, $dateFin);


        foreach ($sorties as $sortie) {

            $todayDate = $today->format('Y-m-d');
            $sortieDate = $sortie->getDatedebut()->format('Y-m-d');
            if ($sortie->getEtat()->getLibelle() != 'En création' and $sortie->getEtat()->getLibelle() !='Annuler' ) {

                // Comparer les dates
                if ($todayDate > $sortieDate) {
                    $etat = $etatRepository->getOrMakeEtat('Fermé', $entityManager); // Fermé
                } elseif ($todayDate == $sortieDate) {
                    $etat = $etatRepository->getOrMakeEtat('En cours', $entityManager); // En cours
                } else {
                    $etat = $etatRepository->getOrMakeEtat('Ouvert', $entityManager); // Ouvert
                }

                $sortie->setEtat($etatRepository->find($etat));
            }

        }
        $entityManager->flush();


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

    #[Route('/sortie/annulation/{id}', name: 'app_sortie_annulation')]
    public function annulation(int $id, Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRepository->find($id);

        if ($request->isMethod('POST'))
        {
            $motif = $request->request->get('motif');
            $sortie->setMotifAnnulation($motif);
            $etat = $etatRepository->getOrMakeEtat('Annuler', $entityManager); // Annuler
            $sortie->setEtat($etatRepository->find($etat));
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('app_sortie');
        }

        return $this->render('sortie/annulation.html.twig', [
            'controller_name' => 'SortieController',
            'sortie' => $sortie,
            'id' => $id,
        ]);

    }
}
