<?php

namespace App\Service\Notification;

use App\Service\PubSub\RedisClient;
use Psr\Log\LoggerInterface;

/**
 * Class PushManager
 *
 * @package App\Service\Notification
 */
class PushManager
{
    const PUSH_CHANNEL_NAME = '<CHANNEL_NAME>';

    /**
     * @var RedisClient
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PushManager constructor.
     *
     * @param RedisClient     $client
     * @param LoggerInterface $logger
     */
    public function __construct(RedisClient $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @param Notification $notification
     *
     * @return int
     */
    public function pushToQueue(Notification $notification)
    {
        $jsonPayload = json_encode($notification->getPayload());
        $pubState = $this->client->publish(self::PUSH_CHANNEL_NAME, $jsonPayload);
        $this->logger->critical(sprintf("Sent notification %s to REDIS: %s.", $jsonPayload, $pubState));

        return $pubState;
    }
}
