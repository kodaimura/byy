<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class CategoryRepository
{
	public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function getAll() {
    	return $this->app->get(PDO::class)
    	->query(
    		"SELECT 
    			id, 
    			category_name
		 	 FROM category"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($category) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"UPDATE category SET
    			category_name = :category_name
		 	 WHERE id = :id"
    	);
    	$stmt->bindValue(':category_name', $category['category_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':id', $category['id'], PDO::PARAM_INT);
    	$stmt->execute();
    }
}