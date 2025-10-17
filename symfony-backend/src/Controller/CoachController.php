<?php 

namespace App\Controller;

use App\Entity\PlayerProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/coach')]
class CoachController extends AbstractController
{
    #[Route('/players', name: 'coach_players', methods: ['GET'])]
    public function listPlayers(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_COACH');

        $players = $em->getRepository(PlayerProfile::class)->findAll();
        $data = array_map(fn(PlayerProfile $p) => [
            'id' => $p->getPlayerAccount()->getId(),
            'email' => $p->getPlayerAccount()->getEmail(),
            'team' => $p->getTeam(),
            'number' => $p->getNumber(),
            'position' => $p->getPosition()
        ], $players);

        return $this->json($data);
    }

    // Aquí se añadirían endpoints para entrenamientos y partidos:
    // /trainings, /trainings/{id}, /trainings/create
    // /matches, /matches/{id}, /matches/create
}
