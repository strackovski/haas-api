<?php

namespace App\Service\Worker;

interface WorkerInterface
{
    public function execute($args);
}
