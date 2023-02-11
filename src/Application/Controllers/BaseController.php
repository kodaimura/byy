<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class BaseController
{
    protected ContainerInterface $app;

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}