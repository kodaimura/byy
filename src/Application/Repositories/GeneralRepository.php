<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class GeneralRepository
{
	public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->logger = $app->get(LoggerInterface::class);
    }

    public function getOneByKey1($key1) {
        $db = $this->app->get(PDO::class);
    	$stmt = $db->prepare(
            "SELECT value FROM general WHERE key1 = :key1"
        );
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->execute();
        return ($stmt->fetch())['value'];
    }

    public function updateByKey1($key1, $value) {
        $db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
            "UPDATE general SET value = :value WHERE key1 = :key1"
        );
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->execute();
    }
}