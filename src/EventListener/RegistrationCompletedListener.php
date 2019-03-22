<?php

namespace App\EventListener;

use App\Service\Manager\UserManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RegistrationCompletedListener
 *
 * @package App\EventListener
 * @author  Vladimir Strackovski <vladimir.strackovski@gmail.com>
 */
class RegistrationCompletedListener implements EventSubscriberInterface
{
    /** @var UserManager */
    private $manager;

    /**
     * RegistrationCompletedListener constructor.
     *
     * @param UserManager $manager
     */
    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => [
                ['onRegistrationCompleted', -10],
            ],
        ];
    }

    /**
     * @param FilterUserResponseEvent $event
     */
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $this->manager->provisionUserDefaults($event->getUser());
    }

}
