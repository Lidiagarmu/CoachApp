<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\Training;
use App\Entity\Game;
use App\Entity\EventImage;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\PlayerProfile;
use App\Repository\EventRepository;

class EventService
{
    private EntityManagerInterface $em;
    private Security $security;
    private SluggerInterface $slugger;
    private string $eventsDirectory;
    private EventRepository $eventRepo;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        SluggerInterface $slugger,
        string $eventsDirectory,
        EventRepository $eventRepo
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->slugger = $slugger;
        $this->eventsDirectory = $eventsDirectory;
        $this->eventRepo = $eventRepo;
        
    }

    /**
     * 🆕 Crear un evento (training o match)
     */
    public function createEvent(array $data, TeamRepository $teamRepo): Event
    {
        $user = $this->security->getUser();

        if (!$user || !$this->security->isGranted('ROLE_COACH')) {
            throw new \Exception('Solo los coaches pueden crear eventos.');
        }

        // Validar tipo de evento
        if (!isset($data['type']) || !in_array($data['type'], ['training', 'match'])) {
            throw new \Exception('Tipo de evento inválido.');
        }

        $team = $teamRepo->find($data['team_id'] ?? null);
        if (!$team) {
            throw new \Exception('Equipo no encontrado.');
        }

        $event = new Event();
        $event->setTitle($data['title'] ?? 'Sin título');
        $event->setDescription($data['description'] ?? '');
        $event->setDate(new \DateTime($data['date']));
        $event->setTime(new \DateTime($data['time']));
        $event->setDuration($data['duration'] ?? 60);
        $event->setLocationName($data['location_name'] ?? '');
        $event->setLocationUrl($data['location_url'] ?? '');
        $event->setType($data['type']);
        $event->setCreatedBy($user);
        $event->setTeam($team);

        $this->em->persist($event);

        // 🆕 Asignar evento a todos los jugadores del equipo
        $players = $team->getPlayers(); // Devuelve array de PlayerProfile
        foreach ($players as $playerProfile) {
            $playerEvent = clone $event; // clonamos el evento base
            $playerEvent->setPlayer($playerProfile); // asignamos el jugador
            $this->em->persist($playerEvent);


    // Clonar entidad hija si aplica
        if ($event->getType() === 'training') {
            $training = new Training();
            $training->setEvent($playerEvent);
            $training->setTrainingType($data['training_type'] ?? '');
            $training->setFocusArea($data['focus_area'] ?? '');
            $training->setCoach($user);
            $this->em->persist($training);
        } else {
            $game = new Game();
            $game->setEvent($playerEvent);
            $game->setOpponent($data['opponent'] ?? '');
            $game->setMatchType($data['match_type'] ?? '');
            $game->setTeam($team);
            $this->em->persist($game);
            }
        }

        $this->em->flush();
        return $event;
    }

    /**
     * 📸 Subir imágenes (máximo 10 por evento)
     */
    public function uploadImages(Event $event, array $files): array
    {
        $user = $this->security->getUser();

        if ($event->getCreatedBy() !== $user && !$this->security->isGranted('ROLE_ADMIN')) {
            throw new \Exception('No tienes permiso para añadir imágenes a este evento.');
        }

        $existingCount = count($event->getImages());
        if ($existingCount + count($files) > 10) {
            throw new \Exception('Máximo 10 imágenes por evento.');
        }

        $uploaded = [];


            foreach ($files as $file) {
                        if (!$file instanceof UploadedFile) continue;

                        $safeFilename = $this->slugger->slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                        // mover archivo a public/uploads/events
                        $file->move($this->eventsDirectory, $newFilename);

                        $image = new EventImage();
                        $image->setEvent($event);
                        $image->setUrl('/uploads/events/' . $newFilename);

                        $this->em->persist($image);
                        $uploaded[] = $image->getUrl();
                    }


        $this->em->flush();

        return $uploaded;
    }

    /**
     * ✏️ Actualizar evento
     */
    public function updateEvent(Event $event, array $data): Event
    {
        $user = $this->security->getUser();

        if ($event->getCreatedBy() !== $user) {
            throw new \Exception('No tienes permiso para editar este evento.');
        }

        $event->setTitle($data['title'] ?? $event->getTitle());
        $event->setDescription($data['description'] ?? $event->getDescription());
        $event->setDate(new \DateTime($data['date'] ?? $event->getDate()->format('Y-m-d')));
        $event->setTime(new \DateTime($data['time'] ?? $event->getTime()->format('H:i')));
        $event->setDuration($data['duration'] ?? $event->getDuration());
        $event->setLocationName($data['location_name'] ?? $event->getLocationName());
        $event->setLocationUrl($data['location_url'] ?? $event->getLocationUrl());

        $this->em->flush();

        return $event;
    }

    /**
     * ❌ Eliminar evento
     */
    public function deleteEvent(Event $event): void
    {
        $user = $this->security->getUser();

        if ($event->getCreatedBy() !== $user) {
            throw new \Exception('No tienes permiso para eliminar este evento.');
        }

        $this->em->remove($event);
        $this->em->flush();
    }


    /**
     * 🆕 Asignar eventos existentes del equipo a un jugador nuevo
     */
    public function assignExistingEventsToNewPlayer(PlayerProfile $player): void
    {
        $team = $player->getTeam();
        if (!$team) {
            return;
        }

        // 1️⃣ Traer todos los eventos existentes del equipo
        $existingEvents = $this->eventRepo->findByTeam($team->getId());

        // 2️⃣ Asignar cada evento al jugador
        foreach ($existingEvents as $event) {
            $event->addPlayer($player);
        }

        // 3️⃣ Persistir los cambios
        $this->em->flush();
    }
}
