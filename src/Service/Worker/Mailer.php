<?php

namespace App\Service\Worker;

use Psr\Log\LoggerInterface;
use \App\Service\Mailer\Send;

class Mailer implements WorkerInterface
{
    /** @var Send */
    protected $send;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param Send $send
     * @param LoggerInterface $logger
     */
    public function __construct(Send $send, LoggerInterface $logger)
    {
        $this->send = $send;
        $this->logger = $logger;
    }

    /**
     * @param array $args
     */
    public function execute($args)
    {
        $args = $args[0];
        $response = $this->send->message(
            $args['from'],
            $args['to'],
            $args['subject'],
            $args['template'],
            $args['templateArgs']
        );
        $this->logger->info($response->getMessage());
    }
}
