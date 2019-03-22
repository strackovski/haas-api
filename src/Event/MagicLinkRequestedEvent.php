<?php

namespace App\Event;

use App\Entity\FundRequest;
use App\Entity\User;
use Aws\Ses\SesClient;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MagicLinkRequestEvent
 *
 * The mlink.requested event is dispatched each time a
 *
 * @package App\Event
 */
class MagicLinkRequestedEvent extends Event
{
    const NAME = 'mlink.requested';

    /**
     * @var User
     */
    protected $user;

    /**
     * FundsRequestedEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;

    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
