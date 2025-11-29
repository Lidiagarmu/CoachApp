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
use App\Entity\PlayerProfile;
use App\Repository\PlayerProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;



#[Route('/api/events')]
class EventController extends AbstractController
{
    private EventService $eventService;
    private EventRepository $eventRepo;
    private TeamRepository $teamRepo;
    private Security $security;
    private PlayerProfileRepository $playerProfileRepo;
    private \Psr\Log\LoggerInterface $logger;

    public function __construct(
        EventService $eventService,
        EventRepository $eventRepo,
        TeamRepository $teamRepo,
        Security $security,
        PlayerProfileRepository $playerProfileRepo,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->eventService = $eventService;
        $this->eventRepo = $eventRepo;
        $this->teamRepo = $teamRepo;
        $this->security = $security;
        $this->playerProfileRepo = $playerProfileRepo;
        $this->logger = $logger;
    }

    #[Route('', name: 'create_event', methods: ['POST'])]
    public function createEvent(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!$data) {
                return $this->json(['error' => 'Datos inválidos o vacíos'], 400);
            }

            // Accept either camelCase (`teamId`) or snake_case (`team_id`) from frontend
            $teamId = $data['teamId'] ?? $data['team_id'] ?? null;

            $event = new Event();
            $event->setTitle($data['title'] ?? 'Evento');
            $event->setType($data['type'] ?? 'training');

            // Validate and parse date/time to avoid throwing uncaught exceptions
            try {
                $date = !empty($data['date']) ? new \DateTime($data['date']) : new \DateTime('now');
            } catch (\Exception $e) {
                return $this->json(['error' => 'Fecha inválida: ' . $e->getMessage()], 400);
            }

            try {
                $time = !empty($data['time']) ? new \DateTime($data['time']) : new \DateTime('now');
            } catch (\Exception $e) {
                return $this->json(['error' => 'Hora inválida: ' . $e->getMessage()], 400);
            }

            $event->setDate($date);
            $event->setTime($time);
            $event->setDuration($data['duration'] ?? 60);
            $team = $this->teamRepo->find($teamId);
            $event->setTeam($team);

            


            // 🔑 Asignar creador: ensure there is an authenticated user
            $user = $this->security->getUser();
            if (!$user) {
                return $this->json(['error' => 'No autenticado. Inicia sesión para crear eventos.'], 401);
            }
            $event->setCreatedBy($user);

            // Asignar jugadores del equipo solo si hay equipo
            if ($team) {
                foreach ($team->getPlayers() as $player) {
                    $event->addPlayer($player);
                }
            }


            // Opcionales: ubicación y descripción
            $event->setLocationName($data['location_name'] ?? '');
            $event->setLocationUrl($data['location_url'] ?? '');
            $event->setDescription($data['description'] ?? '');

            // Normalize training type keys: frontend may send `training_type` or `training_location_type`
            $event->setTrainingType($data['training_type'] ?? $data['training_location_type'] ?? null);
            $event->setFocusArea($data['focus_area'] ?? null);
            $event->setOpponent($data['opponent'] ?? null);
            $event->setMatchType($data['match_type'] ?? null);


            $em->persist($event);
            $em->flush(); // persistir los cambios

            return $this->json([
                'id' => $event->getId(),
                'teamId' => $event->getTeam()?->getId(),
                'message' => 'Evento creado correctamente',
            ], 201);

        } catch (\Throwable $e) {
            // Log full exception (message + trace) for easier debugging
            $this->logger->error('Error creating event: ' . $e->getMessage(), ['exception' => $e]);

            // Return helpful debug info in development. If this is production, consider removing the trace.
            return $this->json([
                'error' => $e->getMessage(),
                'trace' => method_exists($e, 'getTraceAsString') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }


    /**
     * 🆓 PUBLIC - Test simple upload (sin autenticación)
     */
    #[Route('/test-upload', name: 'test_upload', methods: ['POST'])]
    public function testUpload(Request $request): JsonResponse
    {
        try {
            $this->logger->info('🧪 PUBLIC testUpload iniciado');
            
            $files = $request->files->get('images', []);
            
            $this->logger->info('📦 Archivos recibidos', [
                'type' => gettype($files),
                'isArray' => is_array($files),
            ]);

            if (is_iterable($files) && !is_array($files)) {
                $files = iterator_to_array($files);
            }

            $this->logger->info('📊 Después de conversión', ['count' => count($files)]);
            
            foreach ($files as $idx => $file) {
                $this->logger->info("📎 [$idx]", [
                    'type' => get_class($file),
                    'name' => $file instanceof UploadedFile ? $file->getClientOriginalName() : 'N/A',
                    'size' => $file instanceof UploadedFile ? $file->getSize() : 'N/A',
                ]);
            }

            return $this->json([
                'status' => 'ok',
                'filesReceived' => count($files),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('❌ Error: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * 📸 Test - Subir imágenes (máx. 10)
     */
    #[Route('/{id}/images-test', name: 'upload_event_images_test', methods: ['POST'])]
    public function uploadImagesTest(string $id, Request $request): JsonResponse
    {
        try {
            $this->logger->info('🧪 TEST uploadImages iniciado', ['eventId' => $id]);
            
            // Raw debug
            $this->logger->info('📋 Request info', [
                'contentType' => $request->getContentType(),
                'method' => $request->getMethod(),
                'requestFiles_keys' => $request->files->keys(),
            ]);

            // Get images using different methods
            $imagesParam = $request->files->get('images');
            $allFiles = $request->files->all();

            $this->logger->info('📦 Métodos de obtención', [
                'imagesParam_type' => gettype($imagesParam),
                'imagesParam_isEmpty' => empty($imagesParam),
                'allFiles' => array_keys($allFiles),
            ]);

            if ($imagesParam) {
                $this->logger->info('🔍 Análisis de imagesParam', [
                    'type' => get_class($imagesParam),
                    'isArray' => is_array($imagesParam),
                    'isIterable' => is_iterable($imagesParam),
                    'count' => is_countable($imagesParam) ? count($imagesParam) : 'N/A',
                ]);

                if (is_iterable($imagesParam)) {
                    $array = iterator_to_array($imagesParam);
                    $this->logger->info('📊 Archivos en array', ['count' => count($array)]);
                    
                    foreach ($array as $idx => $file) {
                        $this->logger->info("📎 Archivo $idx", [
                            'class' => get_class($file),
                            'isUploadedFile' => $file instanceof UploadedFile,
                            'name' => $file instanceof UploadedFile ? $file->getClientOriginalName() : 'N/A',
                            'size' => $file instanceof UploadedFile ? $file->getSize() : 'N/A',
                        ]);
                    }
                }
            }

            return $this->json(['status' => 'Test completado, revisar logs']);
        } catch (\Exception $e) {
            $this->logger->error('❌ Error en test: ' . $e->getMessage(), ['exception' => $e]);
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
            $this->logger->info('📤 Iniciando carga de imágenes', [
                'eventId' => $id,
                'contentType' => $request->getContentType(),
                'requestFiles' => $request->files->keys()
            ]);

            $files = $request->files->get('images', []);

            $this->logger->info('📁 Archivos recibidos', [
                'type' => gettype($files),
                'count' => is_array($files) ? count($files) : (is_iterable($files) ? iterator_count($files) : 'N/A')
            ]);

            // Normalize: if a single UploadedFile is provided, wrap it into an array
            if ($files instanceof UploadedFile) {
                $files = [$files];
            }

            // If files is a ParameterBag or Traversable, convert to array
            if (!is_array($files) && is_iterable($files)) {
                $files = iterator_to_array($files);
            }

            // Ensure we have an array (could be null or other unexpected type)
            if (!is_array($files)) {
                $files = [];
            }

            $this->logger->info('📊 Archivos después de normalización', ['count' => count($files)]);

            // Validate we have files
            if (empty($files)) {
                return $this->json(['error' => 'No se han proporcionado archivos'], 400);
            }

            // Filter out empty or invalid files
            $validFiles = [];
            foreach ($files as $idx => $file) {
                $this->logger->debug('🔍 Validando archivo', [
                    'index' => $idx,
                    'isUploadedFile' => $file instanceof UploadedFile,
                    'originalName' => $file instanceof UploadedFile ? $file->getClientOriginalName() : 'N/A',
                    'size' => $file instanceof UploadedFile ? $file->getSize() : 'N/A'
                ]);

                if ($file instanceof UploadedFile) {
                    $originalName = $file->getClientOriginalName();
                    if (!empty($originalName) && trim($originalName) !== '') {
                        $validFiles[] = $file;
                    } else {
                        $this->logger->warning('⚠️ Ignorando archivo con nombre vacío', ['index' => $idx]);
                    }
                }
            }

            if (empty($validFiles)) {
                return $this->json(['error' => 'Ninguno de los archivos proporcionados tiene un nombre válido'], 400);
            }

            $this->logger->info('✅ Archivos válidos a procesar', ['count' => count($validFiles)]);

            $uploaded = $this->eventService->uploadImages($event, $validFiles);

            return $this->json([
                'message' => 'Imágenes subidas correctamente',
                'urls' => $uploaded,
            ], 201);
        } catch (\Exception $e) {
            $this->logger->error('Error uploading images: ' . $e->getMessage(), ['exception' => $e]);
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
                'training_type' => $e->getTrainingType(),
                'focus_area' => $e->getFocusArea(),
                'opponent' => $e->getOpponent(),
                'match_type' => $e->getMatchType(),

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
        // Buscar el PlayerProfile por ID
        $playerProfile = $this->playerProfileRepo->find($playerId);

        if (!$playerProfile) {
            return $this->json(['error' => 'Jugador no encontrado'], 404);
        }

        $currentUser = $this->security->getUser();
        $currentPlayerProfile = $currentUser?->getPlayerProfile();

        // Seguridad: jugador solo puede ver sus eventos
        if (!$this->isGranted('ROLE_COACH') && ($currentPlayerProfile === null || $currentPlayerProfile->getId() !== $playerId)) {
            return $this->json(['error' => 'No puedes ver eventos de otros jugadores'], 403);
        }       

        // Buscar todos los eventos de este jugador
        $events = $this->eventRepo->findByPlayer($playerId);




        // Convertir a array para JSON
        $data = array_map(fn(Event $e) => [
            'id' => $e->getId(),
            'title' => $e->getTitle(),
            'description' => $e->getDescription(),
            'type' => $e->getType(),
            'date' => $e->getDate()?->format('Y-m-d') ?? '',
            'time' => $e->getTime()?->format('H:i') ?? '',
            'duration' => $e->getDuration(),
            'location_name' => $e->getLocationName(),
            'location_url' => $e->getLocationUrl(),
            'team' => $e->getTeam() ? [
                'id' => $e->getTeam()->getId(),
                'name' => $e->getTeam()->getName(),
                'shield' => $e->getTeam()->getShield(),
            ] : null,
            'training_type' => $e->getTrainingType(),
            'focus_area' => $e->getFocusArea(),
            'opponent' => $e->getOpponent(),
            'match_type' => $e->getMatchType(),

            'images' => array_map(fn($img) => $img->getUrl(), $e->getImages()->toArray()),
        ], $events);

        return $this->json($data, 200);
    }

}
