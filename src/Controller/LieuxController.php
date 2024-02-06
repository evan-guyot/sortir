<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuxController extends AbstractController
{
    #[Route('/lieux', name: 'app_lieux')]
    public function index(Request $request, LieuRepository $lieuRepository, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response
    {
        $villes = $villeRepository->findAll();

        if ($request->isMethod('POST')) {
            $nomnouveaulieu = $request->request->get('lieu');
            $villeid = $request->request->get('ville');
            $rue = $request->request->get('rue');
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');

            $ville = $villeRepository->find($villeid);

            $lieu = new Lieu();
            $lieu->setNom($nomnouveaulieu);
            $lieu->setVille($ville);
            $lieu->setRue($rue);
            $lieu->setLatitude($latitude);
            $lieu->setLongitude($longitude);

            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez bien ajouté : ".$lieu->getNom());
        }

        return $this->render('lieux/lieux.html.twig', [
            'controller_name' => 'LieuxController',
            'villes' => $villes,
        ]);
    }
}
