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
}