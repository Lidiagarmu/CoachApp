<?php

namespace App\Controller;

use App\Entity\PlayerProfile;
use App\Entity\CoachProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    // =========================================
    // NUEVO: listar jugadores disponibles para el coach
    // =========================================
    #[Route('/available', name: 'get_available_players', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    public function getAvailablePlayers(EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Obtener perfil del coach
        $coachProfile = $em->getRepository(CoachProfile::class)
            ->findOneBy(['userAccount' => $user]);

        if (!$coachProfile) {
            return $this->json(['error' => 'Perfil de entrenador no encontrado'], 404);
        }

        $team = $coachProfile->getTeam();

        // Obtener jugadores que no estén en el equipo del coach
        $qb = $em->getRepository(PlayerProfile::class)->createQueryBuilder('p')
            ->leftJoin('p.team', 't');

        if ($team) {
            $qb->where('t.id IS NULL OR t.id != :teamId')
               ->setParameter('teamId', $team->getId());
        } else {
            $qb->where('t.id IS NULL');
        }

        $players = $qb->getQuery()->getResult();

        $response = array_map(function (PlayerProfile $player) {
            return [
                'id' => $player->getId(),
                'fullName' => $player->getPlayerAccount()->getFullName(),
                'nickname' => $player->getPlayerAccount()->getNickname(),
                'position' => $player->getPosition(),
                'number' => $player->getNumber(),
            ];
        }, $players);

        return $this->json($response);
    }
}
