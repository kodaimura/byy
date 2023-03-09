<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class CouponRepository extends BaseRepository
{

    public function get($customer_id) {
    	$stmt = $this->db->prepare(
    		"SELECT 
    			customer_id,
                coupon_id,
                used_flg,
                create_at,
                update_at
               FROM coupon 
              WHERE customer_id = :customer_id 
                AND deadline > NOW()"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($customer_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM coupon WHERE customer_id = :customer_id"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updateUsedFlg($customer_id) {
        $stmt = $this->db->prepare(
    		"UPDATE coupon SET
                used_flg = '1'
		 	 WHERE customer_id = :customer_id"
    	);
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
    	$stmt->execute();
    }

    public function insert($customer_id, $coupon_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO coupon (
                customer_id,
                coupon_id,
                used_flg,
                deadline
             ) VALUES (
                :customer_id,
                :coupon_id,
                '0',
                NOW() + INTERVAL 12 HOUR
             )"
        );
        $stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindValue(':coupon_id', $coupon_id, PDO::PARAM_STR);
        $stmt->execute();
    }
}