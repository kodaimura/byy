<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class CategoryRepository extends BaseRepository
{
	
    public function getAll() {
    	return $this->db
    	->query(
    		"SELECT 
    			id, 
    			category_name
		 	 FROM category"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($category) {
        $stmt = $this->db->prepare(
    		"UPDATE category SET
    			category_name = :category_name
		 	 WHERE id = :id"
    	);
    	$stmt->bindValue(':category_name', $category['category_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':id', $category['id'], PDO::PARAM_INT);
    	$stmt->execute();
    }
}