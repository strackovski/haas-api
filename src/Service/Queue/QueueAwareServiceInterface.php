<?php

namespace App\Service\Queue;

use SidekiqJob\Client;

/**
 * Interface QueueAwareServiceInterface
 *
 * @package      App\Service\Queue
 * @author       Vladimir Strackovski <vladimir.strackovski@gmail.com>
 * @copyright    2019 Vladimir Strackovski
 */
interface QueueAwareServiceInterface
{
    /**
     * Push task to queue
     *
     * @param $class
     * @param array $args
     * @param bool $retry
     * @param string $queue
     * @return mixed
     */
    public function pushToQueue($class, $args = [], $retry = true, $queue = Client::QUEUE);
}
