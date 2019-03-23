<?php

namespace App\Service\Queue;

use SidekiqJob\Client;

/**
 * Trait QueueAwareServiceInterface
 *
 * @package      App\Service\Queue
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
trait QueueAwareServiceTrait
{
    /** @var Client */
    protected $queue;

    /**
     * Push task to queue
     *
     * @param        $class
     * @param array  $args
     * @param bool   $retry
     * @param string $queue
     *
     * @return string
     */
    public function pushToQueue($class, $args = [], $retry = true, $queue = Client::QUEUE)
    {
        return $this->queue->push($class, $args, $retry, $queue);
    }
}
