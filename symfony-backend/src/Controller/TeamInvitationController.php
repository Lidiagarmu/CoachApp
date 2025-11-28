<?php

namespace App\Controller;

use App\Entity\TeamInvitation;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\PlayerProfile;
use App\Entity\CoachProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\EventService;


#[Route('/api/invitations')]
class TeamInvitationController extends AbstractController
{
    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }


    #[Route('', name: 'create_invitation', methods: ['POST'])]
    #[IsGranted('ROLE_COACH')]
    public function createInvitation(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $playerId = $data['playerId'] ?? null;

        /** @var User $user */
        $user = $this->getUser();
        $coachProfile = $em->getRepository(CoachProfile::class)->findOneBy(['userAccount' => $user]);

        if (!$coachProfile) {
            return $this->json(['error' => 'No se encontró el perfil de entrenador'], 404);
        }

        $team = $coachProfile->getTeam();
        if (!$team) {
            return $this->json(['error' => 'No tienes un equipo asignado'], 400);
        }

        $player = $em->getRepository(User::class)->find($playerId);
        if (!$player) {
            return $this->json(['error' => 'Jugador no encontrado'], 404);
        }

        $playerProfile = $em->getRepository(PlayerProfile::class)->findOneBy(['playerAccount' => $player]);
        if (!$playerProfile) {
            return $this->json(['error' => 'El jugador no tiene perfil válido'], 404);
        }

        // Evitar duplicados
        $existing = $em->getRepository(TeamInvitation::class)->findOneBy([
            'player' => $playerProfile,
            'team' => $team,
            'status' => 'pending'
        ]);
        if ($existing) {
            return $this->json(['error' => 'Ya existe una invitación pendiente para este jugador.'], 409);
        }

        $invitation = new TeamInvitation();
        $invitation->setTeam($team);
        $invitation->setPlayer($playerProfile);
        $invitation->setStatus('pending');
        $invitation->setCoach($coachProfile);

    

        $em->persist($invitation);
        $em->flush();

        return $this->json([
            'message' => 'Invitación enviada correctamente',
            'invitationId' => $invitation->getId()
        ], 201);
    }

    #[Route('/player', name: 'get_player_invitations', methods: ['GET'])]
    #[IsGranted('ROLE_PLAYER')]
    public function getPlayerInvitations(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
         /** @var EntityManagerInterface $em */
        $playerProfile = $em->getRepository(PlayerProfile::class)->findOneBy(['playerAccount' => $user]);

        if (!$playerProfile) {
            return $this->json(['error' => 'Perfil de jugador no encontrado'], 404);
        }

        $invitations = $em->getRepository(TeamInvitation::class)->findBy([
            'player' => $playerProfile,
            'status' => 'pending'
        ]);

        $response = array_map(function (TeamInvitation $inv) {
            return [
                'id' => $inv->getId(),
                'team' => $inv->getTeam()->getName(),
                'status' => $inv->getStatus(),
                'createdAt' => $inv->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $invitations);

        return $this->json($response);
    }

    #[Route('/{id}/respond', name: 'respond_invitation', methods: ['PATCH'])]
    #[IsGranted('ROLE_PLAYER')]
    public function respondInvitation(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        /** @var Request $request */
        $data = json_decode($request->getContent(), true);
        $action = strtolower($data['action'] ?? ''); // accept | reject

        /** @var User $user */
        $user = $this->getUser();
         /** @var EntityManagerInterface $em */
        $playerProfile = $em->getRepository(PlayerProfile::class)->findOneBy(['playerAccount' => $user]);


        /** @var int $id */
        $invitation = $em->getRepository(TeamInvitation::class)->find($id);

        if (!$invitation || $invitation->getPlayer()->getId() !== $playerProfile->getId()) {
            return $this->json(['error' => 'Invitación no encontrada o no autorizada'], 404);
        }

       if ($action === 'accept') {
        // Si ya tiene equipo, bloquear
        if ($playerProfile->getTeam()) {
            return $this->json(['error' => 'Ya perteneces a un equipo'], 400);
        }

        // Aceptar la invitación
        $invitation->setStatus('accepted');
        $playerProfile->setTeam($invitation->getTeam());
        $invitation->getTeam()->addPlayer($playerProfile);

        // Asignar eventos existentes al jugador
        $this->eventService->assignExistingEventsToNewPlayer($playerProfile);

        // Rechazar otras invitaciones pendientes
        $otherInvites = $em->getRepository(TeamInvitation::class)->findBy([
            'player' => $playerProfile,
            'status' => 'pending'
        ]);

        foreach ($otherInvites as $other) {
            if ($other->getId() !== $invitation->getId()) {
                $other->setStatus('rejected');
            }
            }
        } elseif ($action === 'reject') {
            $invitation->setStatus('rejected');
        } else {
            return $this->json(['error' => 'Acción inválida'], 400);
        }

        $em->flush();


        return $this->json(['message' => "Invitación {$action} correctamente"]);
    }



    

}
