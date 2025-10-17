<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/player')]
class PlayerController extends AbstractController
{
    #[Route('/trainings/{id}', name: 'player_training_detail', methods: ['GET'])]
    public function getTraining(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_PLAYER');
        // Obtener solo entrenamiento del jugador según $id
        return $this->json(['training_id' => $id]);
    }

    #[Route('/matches/{id}', name: 'player_match_detail', methods: ['GET'])]
    public function getMatch(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_PLAYER');
        // Obtener solo partido del jugador según $id
        return $this->json(['match_id' => $id]);
    }
}
