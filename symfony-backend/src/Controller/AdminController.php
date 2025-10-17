<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/admin')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_users', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(User::class)->findAll();
        $data = array_map(fn(User $u) => [
            'id' => $u->getId(),
            'email' => $u->getEmail(),
            'roles' => $u->getRoles(),
        ], $users);

        return $this->json($data);
    }

    #[Route('/users/{id}', name: 'admin_user_detail', methods: ['GET'])]
    public function getId(User $user): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/users/{id}', name: 'admin_user_update', methods: ['PUT'])]
    public function updateUser(User $user, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        if (isset($data['roles'])) {
            $user->setRoles($data['roles']);
        }
        $em->flush();

        return $this->json(['status' => 'updated']);
    }

    #[Route('/users/{id}', name: 'admin_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($user);
        $em->flush();

        return $this->json(['status' => 'deleted']);
    }
}
