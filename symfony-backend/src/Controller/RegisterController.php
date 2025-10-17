<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\CoachProfile;
use App\Entity\PlayerProfile;
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

        // 1️⃣ Leer el cuerpo JSON
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $plainPassword = $data['password'] ?? null;
        $type = strtolower($data['type'] ?? ''); // coach o player

        // 2️⃣ Validar campos
        if (!$email || !$plainPassword || !in_array($type, ['coach', 'player'])) {
            return $this->json([
                'error' => 'Email, password, and valid type ("coach" or "player") are required.'
            ], 400);
        }

        // 3️⃣ Comprobar si el usuario ya existe
        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return $this->json(['error' => 'User already exists.'], 409);
        }

        // 4️⃣ Crear usuario base
        $user = new User();
        $user->setEmail($email);

        // 5️⃣ Asignar rol y crear perfil correspondiente
        if ($type === 'coach') {
            $user->setRoles(['ROLE_COACH']);
            $profile = new CoachProfile();
            $profile->setUserAccount($user);
            $profile->setTeamName('Sin equipo');
            $profile->setYearsExperience(0);
            $em->persist($profile);
        } else {
            $user->setRoles(['ROLE_PLAYER']);
            $profile = new PlayerProfile();
            $profile->setPlayerAccount($user);
            $profile->setPosition('Sin asignar');
            $profile->setNumber(0);
            $profile->setTeam('Sin equipo');
            $em->persist($profile);
        }

        // 6️⃣ Hashear contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // 7️⃣ Guardar en DB
        $em->persist($user);
        $em->flush();

        // 8️⃣ Respuesta JSON
        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'type' => $type
        ], 201);
    }
}
