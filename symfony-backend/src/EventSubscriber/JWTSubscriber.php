<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class JWTSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        // Forzamos tipado a nuestra entidad User
        /** @var User|null $user */
        $user = $event->getUser();

        // Si no hay usuario, salimos
        if (!$user instanceof User) {
            return;
        }

        $data = $event->getData();
        $response = $event->getResponse();

        // Añadir info del usuario al body JSON
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nickname' => $user->getNickname(),
            'fullName' => $user->getFullName(),
            'roles' => $user->getRoles(),
        ];


        $event->setData($data);

        // Crear cookie JWT
        $token = $data['token'] ?? null;
        if ($token) {
            $cookie = Cookie::create('jwt_token')
                ->withValue($token)
                ->withHttpOnly(true)
                ->withSecure(false) // ⚠️ True en producción
                ->withSameSite('Lax')
                ->withPath('/')
                ->withExpires(new \DateTime('+1 hour'));

            $response->headers->setCookie($cookie);
        }
    }
}
