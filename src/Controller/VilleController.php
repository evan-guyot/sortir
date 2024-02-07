<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleController extends AbstractController
{
    #[Route('/ville', name: 'app_ville')]
    public function index(Request $request, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $villenom = $request->request->get('ville');
            $villecodepost = $request->request->get('codpos');

            $ville = new Ville();
            $ville->setNom($villenom);
            $ville->setCodePostal($villecodepost);

            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash('success', "Vous avez bien ajouté : " . $ville->getNom());
            return $this->redirectToRoute('app_ville');
        }

        return $this->render('ville/ville.html.twig', [
            'controller_name' => 'VilleController',
        ]);
    }
}
