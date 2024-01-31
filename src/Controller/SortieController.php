<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/sortie')]
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

}
