<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PlayerProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/coach')]
class CoachController extends AbstractController
{
    /**
     * Información del entrenador logueado (perfil y equipo)
     */
    #[Route('/info', name: 'coach_info', methods: ['GET'])]
    public function info(): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_COACH');
        
        /** @var User $user */
        $user = $this->getUser();

        $coachProfile = $user->getCoachProfile();
        $team = $coachProfile?->getTeam();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'fullName' => $user->getFullName(),
            'team' => $team ? [
                'id' => $team->getId(),
                'name' => $team->getName(),
            ] : null,
            'teamName' => $coachProfile?->getTeamName(),
            'yearsExperience' => $coachProfile?->getYearsExperience(),
        ]);
    }

    /**
     * Lista de jugadores pertenecientes al equipo del entrenador logueado
     */
    #[Route('/players', name: 'coach_players', methods: ['GET'])]
    public function listPlayers(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_COACH');

        /** @var User $user */
        $user = $this->getUser();

        $coachProfile = $user->getCoachProfile();
        $team = $coachProfile?->getTeam();

        if (!$team) {
            return $this->json(['error' => 'El entrenador no tiene equipo asignado.'], 404);
        }

        $players = $team->getPlayers();

        if ($players->isEmpty()) {
            return $this->json(['message' => 'El equipo no tiene jugadores asignados.'], 200);
        }

        $data = array_map(static function (PlayerProfile $player) {
            $account = $player->getPlayerAccount();
            return [
                'id' => $account->getId(),
                'email' => $account->getEmail(),
                'fullName' => $account->getFullName(),
                'nickname' => $account->getNickname(),
                'age' => $account->getAge(),
                'number' => $player->getNumber(),
                'position' => $player->getPosition(),
            ];
        }, $players->toArray());

        return $this->json([
            'team' => $team->getName(),
            'players' => $data,
        ]);
    }

    // 🔜 Endpoints futuros:
    // /trainings, /trainings/{id}, /trainings/create
    // /matches, /matches/{id}, /matches/create
}
