<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ParticipantController extends AbstractController
{
    #[Route('/participants', "app_participants")]
    public function allParticipants(ParticipantRepository $participantRepository)
    {
        $participants = $participantRepository->findAll();

        return $this->render("participant/all.html.twig", [
            'participants' => $participants
        ]);
    }

    #[Route('/participant/disable', "app_participants_disable")]
    public function disable(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->disable($userId);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/participant/enable', "app_participants_enable")]
    public function enable(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->enable($userId, $participantRepository);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/participant/delete', "app_participants_delete")]
    public function delete(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->delete($userId);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/participant/{id}', "app_participant_id")]
    public function participantId(int $id, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);

        return $this->render("participant/one.html.twig", [
            'participant' => $participant
        ]);
    }
}
