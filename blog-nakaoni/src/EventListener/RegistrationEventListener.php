<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationEventListener implements EventSubscriberInterface
{

    /**
     * @param EntityManagerInterface $manager
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED =>
                'onRegistrationCompleted'
        );
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setRoles(array('ROLE_ABONNE'));

        $this->manager->persist($user);
        $this->manager->flush();
    }
}