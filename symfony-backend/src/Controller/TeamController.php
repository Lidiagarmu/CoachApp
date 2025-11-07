<?php

namespace App\Controller;

use App\Entity\Team;
use App\Entity\PlayerProfile;
use App\Entity\CoachProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/team')]
class TeamController extends AbstractController
{
    #[Route('', name: 'team_create', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function createTeam(Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $coachProfile = $em->getRepository(CoachProfile::class)
            ->findOneBy(['userAccount' => $user]);

        if (!$coachProfile) {
            return $this->json(['error' => 'Perfil de entrenador no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);

        // Validación mínima
        if (empty($data['name'])) {
            return $this->json(['error' => 'Nombre de equipo requerido'], 400);
        }

        // Verificar nombre único
        $existingTeam = $em->getRepository(Team::class)
            ->findOneBy(['name' => $data['name']]);

        if ($existingTeam) {
            return $this->json(['error' => 'Nombre de equipo ya existe'], 400);
        }

        $team = new Team();
        $team->setName($data['name']);
        $team->setCoach($coachProfile);

        if (!empty($data['shield'])) {
            $team->setShield($data['shield']);
        }

        // Añadir jugadores si vienen IDs y existen
        if (!empty($data['playerIds']) && is_array($data['playerIds'])) {
            foreach ($data['playerIds'] as $playerId) {
                $player = $em->getRepository(PlayerProfile::class)->find($playerId);
                if ($player && $player->getTeam() === null) {
                    $team->addPlayer($player);
                }
            }
        }

        $em->persist($team);
        $em->flush();

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'shield' => $team->getShield(),
            'players' => array_map(fn($p) => $p->getPlayerAccount()->getId(), $team->getPlayers()->toArray())
        ]);
    }

    #[Route('', name: 'team_get', methods: ['GET'])]
    #[IsGranted('ROLE_COACH')]
    public function getTeam(EntityManagerInterface $em): JsonResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $coachProfile = $em->getRepository(CoachProfile::class)
            ->findOneBy(['userAccount' => $user]);

        if (!$coachProfile) {
            return $this->json(['error' => 'Perfil de entrenador no encontrado'], 404);
        }

        $team = $em->getRepository(Team::class)
            ->findOneBy(['coach' => $coachProfile]);

        if (!$team) {
            return $this->json(['error' => 'No tiene equipo creado aún'], 404);
        }

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'shield' => $team->getShield(),
            'players' => array_map(fn(PlayerProfile $p) => [
                'id' => $p->getPlayerAccount()->getId(),
                'fullName' => $p->getPlayerAccount()->getFullName(),
                'nickname' => $p->getPlayerAccount()->getNickname()
            ], $team->getPlayers()->toArray())
        ]);
    }

    // método para añadir un jugador al equipo
    #[Route('/{id}/add-player', name: 'team_add_player', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function addPlayer(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $team = $em->getRepository(Team::class)->find($id);
        if (!$team) {
            return $this->json(['error' => 'Equipo no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['playerId'])) {
            return $this->json(['error' => 'Se requiere playerId'], 400);
        }

        $player = $em->getRepository(PlayerProfile::class)->find($data['playerId']);
        if (!$player) {
            return $this->json(['error' => 'Jugador no encontrado'], 404);
        }

        if ($player->getTeam() !== null) {
            return $this->json(['error' => 'Jugador ya pertenece a un equipo'], 400);
        }

        $team->addPlayer($player);
        $em->persist($team);
        $em->flush();

        return $this->json(['success' => 'Jugador añadido', 'playerId' => $player->getPlayerAccount()->getId()]);
    }


    #[Route('/available', name: 'team_available', methods: ['GET'])]
    public function getAvailableTeams(EntityManagerInterface $em): JsonResponse
    {
        $teams = $em->getRepository(Team::class)->findAll();

        $response = array_map(fn(Team $team) => [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'shield' => $team->getShield()
        ], $teams);

        return $this->json($response);
    }

     //método para permitir que el coach edite nombre o escudo del equipo
    #[Route('/{id}', name: 'team_update', methods: ['PUT'])]
    #[IsGranted('ROLE_COACH')]
    public function updateTeam(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $team = $em->getRepository(Team::class)->find($id);
        if (!$team) {
            return $this->json(['error' => 'Equipo no encontrado'], 404);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $coachProfile = $em->getRepository(CoachProfile::class)
            ->findOneBy(['userAccount' => $user]);

        if ($team->getCoach() !== $coachProfile) {
            return $this->json(['error' => 'No autorizado para editar este equipo'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!empty($data['name'])) {
            $team->setName($data['name']);
        }
        if (!empty($data['shield'])) {
            $team->setShield($data['shield']);
        }

        $em->flush();

        return $this->json(['success' => true, 'message' => 'Equipo actualizado correctamente']);
    }

     //método para eliminar un equipo
    #[Route('/{id}', name: 'team_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_COACH')]
    public function deleteTeam(int $id, EntityManagerInterface $em): JsonResponse
    {
        $team = $em->getRepository(Team::class)->find($id);
        if (!$team) {
            return $this->json(['error' => 'Equipo no encontrado'], 404);
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $coachProfile = $em->getRepository(CoachProfile::class)
            ->findOneBy(['userAccount' => $user]);

        if ($team->getCoach() !== $coachProfile) {
            return $this->json(['error' => 'No autorizado para eliminar este equipo'], 403);
        }

        // Eliminar referencias de jugadores
        foreach ($team->getPlayers() as $player) {
            $player->setTeam(null);
        }

        $em->remove($team);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Equipo eliminado correctamente']);
    }


    //método para eliminar un jugador del equipo
    #[Route('/{id}/remove-player', name: 'team_remove_player', methods: ['PATCH'])]
    #[IsGranted('ROLE_COACH')]
    public function removePlayer(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $team = $em->getRepository(Team::class)->find($id);
        if (!$team) {
            return $this->json(['error' => 'Equipo no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);
        if (empty($data['playerId'])) {
            return $this->json(['error' => 'Se requiere playerId'], 400);
        }

        $player = $em->getRepository(PlayerProfile::class)->find($data['playerId']);
        if (!$player) {
            return $this->json(['error' => 'Jugador no encontrado'], 404);
        }

        if ($player->getTeam() !== $team) {
            return $this->json(['error' => 'El jugador no pertenece a este equipo'], 400);
        }

        $team->removePlayer($player);
        $em->flush();

        return $this->json(['success' => true, 'message' => 'Jugador eliminado del equipo']);
    }


    // método para Oçbtener info de un equipo por ID, útil para admin/debug
    #[Route('/{id}', name: 'team_get_by_id', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function getTeamById(int $id, EntityManagerInterface $em): JsonResponse
    {
        $team = $em->getRepository(Team::class)->find($id);
        if (!$team) {
            return $this->json(['error' => 'Equipo no encontrado'], 404);
        }

        return $this->json([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'shield' => $team->getShield(),
            'coach' => $team->getCoach()?->getUserAccount()?->getFullName(),
            'players' => array_map(fn(PlayerProfile $p) => [
                'id' => $p->getId(),
                'name' => $p->getPlayerAccount()->getFullName()
            ], $team->getPlayers()->toArray())
        ]);
    }





}
