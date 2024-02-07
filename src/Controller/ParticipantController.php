<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/participants')]
class ParticipantController extends AbstractController
{
    #[Route('/administration', "app_participants")]
    public function allParticipants(ParticipantRepository $participantRepository)
    {
        $participants = $participantRepository->findAll();

        return $this->render("participant/all.html.twig", [
            'participants' => $participants
        ]);
    }

    #[Route('/disable', "app_participant_disable")]
    public function disable(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->disable($userId);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/enable', "app_participant_enable")]
    public function enable(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->enable($userId, $participantRepository);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/delete', "app_participant_delete")]
    public function delete(Request $request, ParticipantRepository $participantRepository): Response
    {
        $userId = $request->get('userId');
        $participantRepository->delete($userId);

        return $this->redirectToRoute('app_participants');
    }

    #[Route('/{id}', "app_participant_id")]
    public function participantId(int $id, ParticipantRepository $participantRepository)
    {
        $participant = $participantRepository->find($id);

        return $this->render("participant/one.html.twig", [
            'participant' => $participant
        ]);
    }
}
