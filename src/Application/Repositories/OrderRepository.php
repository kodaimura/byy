<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class OrderRepository
{
	public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function get($customer_id, $product_id) {
    	$db = $this->app->get(PDO::class);
    	$stmt = $db->prepare(
    		"SELECT 
    			customer_id,
    			product_id,
    			count
		 	 FROM order_history
		 	 WHERE customer_id = :customer_id
               AND product_id = :product_id"
    	);
    	$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
        return $stmt->fetch();
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
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"INSERT INTO order_history (
    			customer_id,
                product_id,
                count
		 	 ) VALUES (
		 	 	:customer_id,
		 	 	:product_id,
		 	 	:count
		 	 )"
    	);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $values['product_id'], PDO::PARAM_INT);
        $stmt->bindValue(':count', $values['count'], PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function update($values) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"UPDATE order_history SET
    			count = count + :count
		 	 WHERE customer_id = :customer_id
               AND product_id = :product_id"
    	);
        $stmt->bindValue(':count', $values['count'], PDO::PARAM_INT);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $values['product_id'], PDO::PARAM_INT);
    	$stmt->execute();
    }

}