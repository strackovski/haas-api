<?php

namespace App\EventListener;

use App\Event\FundsRequestedEvent;
use App\Event\FundsSubmittedEvent;
use App\Event\MagicLinkRequestedEvent;
use App\Event\MagicLinkUsageEvent;
use App\Service\Mailer\SimpleEmailService;

/**
 * Class MagicLinkRequestListener
 *
 * @package App\EventListener
 */
class MagicLinkRequestListener
{
    const MESSAGE_FORMAT = "The magic link %s is.";
    const DEFAULT_TEMPLATE = "@mailing/magic_link.html.twig";

    /**
     * @var SimpleEmailService
     */
    private $mailer;

    /**
     * MagicLinkRequestListener constructor.
     *
     * @param SimpleEmailService $mailer
     */
    public function __construct(SimpleEmailService $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param MagicLinkRequestedEvent $event
     *
     * @return null
     */
    public function onMagicLinkRequest(MagicLinkRequestedEvent $event)
    {
        if (!$event->getUser()->isMLinkValid()) {
//            return null;
        }

        try {
            $this->mailer->sendMagicLink($event->getUser());
        } catch (\Exception $e) {
            return null;
        }
    }
}
