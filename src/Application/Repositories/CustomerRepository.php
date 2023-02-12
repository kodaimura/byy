<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class CustomerRepository extends BaseRepository
{

    public function getById($customer_id) {
    	$stmt = $this->db->prepare(
    		"SELECT 
    			customer_id,
    			customer_name,
    			visit_count,
    			total_payment,
    			update_at
		 	 FROM customer
		 	 WHERE customer_id = :customer_id"
    	);
    	$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
    	$stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function upsert($values) {
    	$rows = $this->getById($values['customer_id']);
    	if (empty($rows)) {
    		$this->insert($values);
    	} else {
    		$this->update($values);
    	}
    }

    public function insert($values) {
    	$stmt = $this->db->prepare(
    		"INSERT INTO customer (
    			customer_id,
    			customer_name, 
    			visit_count,
    			total_payment
		 	 ) VALUES (
		 	 	:customer_id,
		 	 	:customer_name,
		 	 	1,
		 	 	:total_payment
		 	 )"
    	);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
    	$stmt->bindValue(':customer_name', $values['customer_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':total_payment', $values['total_payment'], PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function update($values) {
    	$stmt = $this->db->prepare(
    		"UPDATE customer SET
    			customer_name = :customer_name,
    			visit_count = visit_count + 1,
    			total_payment = total_payment + :total_payment
		 	 WHERE customer_id = :customer_id"
    	);
    	$stmt->bindValue(':customer_name', $values['customer_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':total_payment', $values['total_payment'], PDO::PARAM_INT);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
    	$stmt->execute();
    }

}