<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CoachProfile;
use App\Entity\PlayerProfile;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;
        $type = strtolower($data['type'] ?? ''); // coach | player
        $fullName = $data['fullName'] ?? null;
        $nickname = $data['nickname'] ?? null;
        $age = $data['age'] ?? null;

        if (!$email || !$plainPassword || !in_array($type, ['coach', 'player'])) {
            return $this->json(['error' => 'Email, password, and valid type ("coach" or "player") are required.'], 400);
        }

        // Ya existe?
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json(['error' => 'User already exists.'], 409);
        }

        // Crear usuario base
        $user = new User();
        $user->setEmail($email)
             ->setFullName($fullName)
             ->setNickname($nickname)
             ->setAge($age);

        // Hashear contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Si es coach
        if ($type === 'coach') {
            $user->setRoles(['ROLE_COACH']);

            $profile = new CoachProfile();
            $profile->setUserAccount($user);
            $profile->setTeamName($data['teamName'] ?? 'Equipo sin nombre');
            $profile->setYearsExperience($data['yearsExperience'] ?? 0);

            // Crear equipo vinculado al coach
            $team = new Team();
            $team->setName($profile->getTeamName());
            $team->setCoach($profile);
            $profile->setTeam($team);

            $em->persist($team);
            $em->persist($profile);
        }

        // Si es player
        if ($type === 'player') {
            $user->setRoles(['ROLE_PLAYER']);
            $teamId = $data['teamId'] ?? null;

            if (!$teamId) {
                return $this->json(['error' => 'teamId is required for players'], 400);
            }

            $team = $em->getRepository(Team::class)->find($teamId);
            if (!$team) {
                return $this->json(['error' => 'Team not found'], 404);
            }

            $profile = new PlayerProfile();
            $profile->setPlayerAccount($user);
            $profile->setPosition($data['position'] ?? 'Sin asignar');
            $profile->setNumber($data['number'] ?? 0);
            $profile->setTeam($team);

            $team->addPlayer($profile);
            $em->persist($profile);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'fullName' => $user->getFullName(),
            'nickname' => $user->getNickname(),
            'age' => $user->getAge(),
            'type' => $type
        ], 201);
    }
}
