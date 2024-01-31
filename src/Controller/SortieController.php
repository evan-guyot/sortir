<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'app_sortie')]
    public function index(SiteRepository $siteRepository, SortieRepository $sortieRepository, ParticipantRepository $participantRepository): Response
    {
        $User = $participantRepository->find(5);

        $Today = time();

        $sites = $siteRepository->findAll();
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
            'sites' => $sites,
            'sorties' => $sorties,
            'today' => $Today,
            'user' => $User,
        ]);
    }
}
