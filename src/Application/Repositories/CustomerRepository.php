<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;

class CustomerRepository
{
	public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function getById($customer_id) {
    	$db = $this->app->get(PDO::class);
    	$stmt = $db->prepare(
    		"SELECT 
    			customer_id,
    			customer_name,
    			visits_count,
    			cumulative_payment,
    			update_at
		 	 FROM customer
		 	 WHERE customer_id = :customer_id"
    	);
    	$stmt->bindValue(':customer_id', $customer_id, PDO::PARAM_STR);
    	$stmt->execute();
        return $stmt->fetch();
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
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"INSERT INTO customer (
    			customer_id,
    			customer_name, 
    			visits_count,
    			cumulative_payment
		 	 ) VALUES (
		 	 	:customer_id,
		 	 	:customer_name,
		 	 	1,
		 	 	:cumulative_payment
		 	 )"
    	);
    	$stmt->bindValue(':customer_id', $values['customer_id'], PDO::PARAM_STR);
    	$stmt->bindValue(':customer_name', $values['customer_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':cumulative_payment', $values['cumulative_payment'], PDO::PARAM_INT);
    	$stmt->execute();

    	$this->app->get(LoggerInterface::class)->info(json_encode($db->errorInfo()));
    }

    public function update($values) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"UPDATE customer SET
    			customer_name = :customer_name,
    			visits_count = visits_count + 1,
    			cumulative_payment = cumulative_payment + :cumulative_payment,
		 	 WHERE customer_id = :customer_id"
    	);
    	$stmt->bindValue(':customer_name', $values['ustomer_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':cumulative_payment', $values['cumulative_payment'], PDO::PARAM_INT);
    	$stmt->bindValue(':customer_id', $values['$ustomer_id'], PDO::PARAM_STR);
    	$stmt->execute();
    }

}