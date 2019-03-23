<?php

namespace App\Service\PubSub;

use Predis\Client;

/**
 * Class AccountSettingsManager
 *
 * @package App\Service\PubSub
 */
class RedisClient
{
    /**
     * REDIS
     */
    const DEFAULT_ADDRESS = '127.0.0.1';
    const DEFAULT_PORT = 6379;
    const DEFAULT_SCHEME = 'tcp';

    /**
     * @var Client
     */
    private $client;

    /**
     * RedisClient constructor.
     *
     */
    public function __construct(
//        $redisAddress = self::DEFAULT_ADDRESS,
//        $redisPort = self::DEFAULT_PORT,
//        $redisScheme = self::DEFAULT_SCHEME
    )
    {
        $this->client = new Client(
            [
                'scheme' => self::DEFAULT_SCHEME,
                'host' => self::DEFAULT_ADDRESS,
                'port' => self::DEFAULT_PORT,
            ]
        );
    }

    /**
     * Publish message
     *
     * @param $channel
     * @param $message
     *
     * @return int
     */
    public function publish($channel, $message)
    {
        return $this->client->publish($channel, $message);
    }
}
