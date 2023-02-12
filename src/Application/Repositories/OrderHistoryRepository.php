<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class OrderHistoryRepository extends BaseRepository
{

    public function get($customer_id, $product_id) {
    	$stmt = $this->db->prepare(
    		"SELECT 
    			customer_id,
    			product_id,
    			order_count
		 	 FROM order_history
		 	 WHERE customer_id = :customer_id
               AND product_id = :product_id"
    	);
    	$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function upsert($values) {
    	$rows = $this->get($values['customer_id'], $values['product_id']);
    	if (empty($rows)) {
    		$this->insert($values);
    	} else {
    		$this->update($values);
    	}
    }

    public function insert($values) {
    	$stmt = $this->db->prepare(
    		"INSERT INTO order_history (
    			customer_id,
                product_id,
                order_count
		 	 ) VALUES (
		 	 	:customer_id,
		 	 	:product_id,
		 	 	:order_count
		 	 )"
    	);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $values['product_id'], PDO::PARAM_INT);
        $stmt->bindValue(':order_count', $values['order_count'], PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function update($values) {
    	$stmt = $this->db->prepare(
    		"UPDATE order_history SET
    			order_count = order_count + :order_count
		 	 WHERE customer_id = :customer_id
               AND product_id = :product_id"
    	);
        $stmt->bindValue(':order_count', $values['order_count'], PDO::PARAM_INT);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $values['product_id'], PDO::PARAM_INT);
    	$stmt->execute();
    }

}