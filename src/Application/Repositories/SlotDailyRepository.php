<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class SlotDailyRepository extends BaseRepository
{

    public function get($customer_id) {
    	$stmt = $this->db->prepare(
    		"SELECT 
    			customer_id,
                result,
                used_flg,
                create_at,
                update_at
               FROM slot_daily 
              WHERE customer_id = :customer_id 
                AND (create_at > NOW() - INTERVAL 12 HOUR)"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($customer_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM slot_daily WHERE customer_id = :customer_id"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updateUsedFlg($customer_id) {
        $stmt = $this->db->prepare(
    		"UPDATE slot_daily SET
                used_flg = '1'
		 	 WHERE customer_id = :customer_id"
    	);
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
    	$stmt->execute();
    }

    public function insert($customer_id, $result) {
        $stmt = $this->db->prepare(
            "INSERT INTO slot_daily (
                customer_id,
                result,
                used_flg
             ) VALUES (
                :customer_id,
                :result,
                '0'
             )"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindValue(':result', $result, PDO::PARAM_STR);
        $stmt->execute();
    }
}