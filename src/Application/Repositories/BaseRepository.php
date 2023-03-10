<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use App\Application\Repositories\GeneralRepository;

class BaseRepository
{

    protected ContainerInterface $app;

    protected LoggerInterface $logger;

    protected PDO $db;

	public function __construct(LoggerInterface $logger, PDO $db)
    {
        $this->logger = $logger;
        $this->db = $db;
    }
}