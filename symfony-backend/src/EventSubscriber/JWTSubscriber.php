<?php
namespace App\EventSubscriber;

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

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $token = $event->getData()['token'];

        // Crear cookie segura
        $cookie = Cookie::create('jwt_token')
            ->withValue($token)
            ->withHttpOnly(true) // no accesible por JS
            ->withSecure(false) // ⚠️ en desarrollo "false", en producción pon "true"
            ->withSameSite('Lax')
            ->withPath('/')
            ->withExpires(new \DateTime('+1 hour'));

        $event->getResponse()->headers->setCookie($cookie);

        // También puedes eliminar el campo "token" del body si no quieres exponerlo
        // unset($event->getData()['token']);
    }
}
