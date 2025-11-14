<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class MeController extends AbstractController
{
   #[Route('/api/me', name: 'api_me', methods: ['GET'])]
public function me(#[CurrentUser] ?User $user): JsonResponse
{
    if (!$user) {
        return $this->json(['error' => 'Unauthorized'], 401);
    }

    $playerProfile = $user->getPlayerProfile(); // Puede ser null si es coach

    return $this->json([
        'id' => $user->getId(),
        'email' => $user->getEmail(),
        'nickname' => $user->getNickname(),
        'fullName' => $user->getFullName(),
        'roles' => $user->getRoles(),
        'playerProfile' => $playerProfile ? [
            'id' => $playerProfile->getId(),
            'team' => $playerProfile->getTeam() ? [
                'id' => $playerProfile->getTeam()->getId(),
                'name' => $playerProfile->getTeam()->getName()
            ] : null,
            // puedes incluir otros campos de PlayerProfile
            'position' => $playerProfile->getPosition(),
            'number' => $playerProfile->getNumber()
        ] : null,
    ]);
}

}
