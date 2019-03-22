<?php

namespace App\Service\Worker;

use Psr\Log\LoggerInterface;

class Example implements WorkerInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute($args)
    {
        $this->logger->info('WORKER EXAMPLE TEST');
        $this->logger->info(print_r($args, true));
    }
}
