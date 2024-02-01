<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    #[Route('/{id}', "app_participant_id")]
    public function participantId(int $id, ParticipantRepository $participantRepository)
    {
        $partcipant = $participantRepository->find($id);

        return $this->render("participant/one.html.twig", [
            'participant' => $partcipant
        ]);
    }
}
