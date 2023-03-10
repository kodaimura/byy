<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class GeneralRepository extends BaseRepository
{

    public function getOneByKey1($key1) {
        $stmt = $this->db->prepare(
            "SELECT value FROM general WHERE key1 = :key1"
        );
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->execute();
        return ($stmt->fetch(PDO::FETCH_ASSOC))['value'];
    }

    public function getAllByKey1($key1) {
        $stmt = $this->db->prepare(
            "SELECT key1, key2, value, remarks FROM general WHERE key1 = :key1"
        );
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateByKey1($key1, $value) {
        $stmt = $this->db->prepare(
            "UPDATE general SET value = :value WHERE key1 = :key1"
        );
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->execute();
    }

        public function updateByKey1Key2($key1, $key2, $value) {
        $stmt = $this->db->prepare(
            "UPDATE general SET value = :value WHERE key1 = :key1 and key2 = :key2"
        );
        $stmt->bindValue(':value', $value, PDO::PARAM_STR);
        $stmt->bindValue(':key1', $key1, PDO::PARAM_STR);
        $stmt->bindValue(':key2', $key2, PDO::PARAM_STR);
        $stmt->execute();
    }
}