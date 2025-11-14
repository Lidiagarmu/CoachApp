<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\TeamRepository;
use App\Service\EventService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;



#[Route('/api/events')]
class EventController extends AbstractController
{
    private EventService $eventService;
    private EventRepository $eventRepo;
    private TeamRepository $teamRepo;
    private Security $security;

    public function __construct(
        EventService $eventService,
        EventRepository $eventRepo,
        TeamRepository $teamRepo,
        Security $security
    ) {
        $this->eventService = $eventService;
        $this->eventRepo = $eventRepo;
        $this->teamRepo = $teamRepo;
        $this->security = $security;
    }

    /**
     * 🆕 Crear evento (Training o Game)
     */
    #[Route('', name: 'create_event', methods: ['POST'])]
    public function createEvent(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return $this->json(['error' => 'Datos inválidos o vacíos'], 400);
            }

            $event = $this->eventService->createEvent($data, $this->teamRepo);

        return $this->json([
            'id' => $event->getId(),
            'teamId' => $event->getTeam()?->getId(), // ✅ null safe
            'message' => 'Evento creado correctamente',
        ], 201);

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * 📸 Subir imágenes (máx. 10)
     */
    #[Route('/{id}/images', name: 'upload_event_images', methods: ['POST'])]
    public function uploadImages(string $id, Request $request): JsonResponse
    {
        $event = $this->eventRepo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Evento no encontrado'], 404);
        }

        try {
            $files = $request->files->get('images', []);
            $uploaded = $this->eventService->uploadImages($event, $files);

            return $this->json([
                'message' => 'Imágenes subidas correctamente',
                'urls' => $uploaded,
            ], 201);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * 👀 Listar eventos por equipo
     */
    #[Route('/team/{teamId}', name: 'list_team_events', methods: ['GET'])]
    public function listByTeam(string $teamId): JsonResponse
    {
        $user = $this->security->getUser();
        $events = $this->eventRepo->findBy(['team' => $teamId], ['date' => 'ASC']);

        // 🔐 Reglas de visibilidad
        if ($this->isGranted('ROLE_PLAYER')) {
            /** @var User $user */
            $playerTeam = $user->getPlayerProfile()?->getTeam();
            if (!$playerTeam || $playerTeam->getId() !== (int)$teamId) {
                return $this->json(['error' => 'No puedes ver eventos de otros equipos'], 403);
            }
        }

        $data = array_map(function (Event $e) {
            $team = $e->getTeam();
            return [
                'id' => $e->getId(),
                'title' => $e->getTitle(),
                'description' => $e->getDescription(),
                'date' => $e->getDate()->format('Y-m-d'),
                'time' => $e->getTime()->format('H:i'),
                'duration' => $e->getDuration(),
                'type' => $e->getType(),
                'location_name' => $e->getLocationName(),
                'location_url' => $e->getLocationUrl(),
                'team' => $team ? [
                    'id' => $team->getId(),
                    'name' => $team->getName(),
                    'shield' => $team->getShield(),
                ] : null,
                'images' => array_map(fn($img) => $img->getUrl(), $e->getImages()->toArray()),
            ];
        }, $events);

        return $this->json($data, 200);
    }

    /**
     * ✏️ Editar evento
     */
    #[Route('/{id}', name: 'update_event', methods: ['PUT'])]
    public function updateEvent(string $id, Request $request): JsonResponse
    {
        $event = $this->eventRepo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Evento no encontrado'], 404);
        }

        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return $this->json(['error' => 'Datos inválidos'], 400);
            }

            $this->eventService->updateEvent($event, $data);
            return $this->json(['message' => 'Evento actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * ❌ Eliminar evento
     */
    #[Route('/{id}', name: 'delete_event', methods: ['DELETE'])]
    public function deleteEvent(string $id): JsonResponse
    {
        $event = $this->eventRepo->find($id);
        if (!$event) {
            return $this->json(['error' => 'Evento no encontrado'], 404);
        }

        try {
            $this->eventService->deleteEvent($event);
            return $this->json(['message' => 'Evento eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }


    #[Route('/player/{playerId}', name: 'list_player_events', methods: ['GET'])]
    public function listByPlayer(int $playerId): JsonResponse
    {
        // Obtener el playerProfile
        $playerProfile = $this->security->getUser()->getPlayerProfile();

        // Seguridad: un jugador solo puede ver SUS propios eventos
        if (!$this->isGranted('ROLE_COACH') && (!$playerProfile || $playerProfile->getId() !== $playerId)) {
            return $this->json(['error' => 'No puedes ver eventos de otros jugadores'], 403);
        }

        // Buscar todos los eventos asignados a ese jugador
        $events = $this->eventRepo->findBy(['player' => $playerProfile], ['date' => 'ASC']);

        // Convertir a JSON
        $data = array_map(function (Event $e) {
            $team = $e->getTeam();
            return [
                'id' => $e->getId(),
                'title' => $e->getTitle(),
                'description' => $e->getDescription(),
                'date' => $e->getDate()->format('Y-m-d'),
                'time' => $e->getTime()->format('H:i'),
                'duration' => $e->getDuration(),
                'type' => $e->getType(),
                'location_name' => $e->getLocationName(),
                'location_url' => $e->getLocationUrl(),
                'team' => $team ? [
                    'id' => $team->getId(),
                    'name' => $team->getName(),
                    'shield' => $team->getShield(),
                ] : null,
                'images' => array_map(
                    fn($img) => $img->getUrl(),
                    $e->getImages()->toArray()
                ),
            ];
        }, $events);

        return $this->json($data, 200);
    }

}
