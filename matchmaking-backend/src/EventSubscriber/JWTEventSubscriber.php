<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        
        // S'assurer que le token contient getUserIdentifier() (email) et non getUsername()
        $payload = $event->getData();
        
        // Remplacer 'username' par getUserIdentifier() si l'utilisateur a cette méthode
        if (method_exists($user, 'getUserIdentifier')) {
            $payload['username'] = $user->getUserIdentifier();
        }
        
        $event->setData($payload);
    }
}

