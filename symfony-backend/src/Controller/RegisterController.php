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
            return $this->json(['error' => 'Email, password y tipo de usuario ("coach" o "player") son obligatorios.'], 400);
        }

        // Comprobar si ya existe
        if ($em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->json(['error' => 'El usuario ya existe.'], 409);
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
            $profile->setYearsExperience($data['yearsExperience'] ?? 0);
            $profile->setTeamName($data['teamName'] ?? null); // opcional

            // ❌ Ya no se crea el Team aquí. El coach podrá hacerlo desde su panel.
            $em->persist($profile);
                
            // Crear equipo solo si se envió un nombre
                if (!empty($data['teamName'])) {
                    $team = new Team();
                    $team->setName($data['teamName']);
                    $team->setCoach($profile);
                    $em->persist($team);
                 }
        }

        // Si es player
        if ($type === 'player') {
            $user->setRoles(['ROLE_PLAYER']);

            $profile = new PlayerProfile();
            $profile->setPlayerAccount($user);
            $profile->setPosition($data['position'] ?? 'Sin asignar');
            $profile->setNumber($data['number'] ?? 0);
            // ❌ No se asigna equipo al registrarse
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
