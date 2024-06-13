<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTAuthenticationSuccessSubscriber implements EventSubscriberInterface
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = [];
        foreach ($event->getData() as $key => $value) {
            $data[$key] = $value;
        }

        $user = $event->getUser();
        $data['userOrganizations'] = [];
        if ($user instanceof User) {
            $data['fullName'] = $user->getFullName();
            foreach ($user->getUserOrganizations()->toArray() as $userOrganization) {
                $data['userOrganizations'][] = [
                    'id' => $userOrganization->getOrganization()->getId(),
                ];
            }
        }

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}
