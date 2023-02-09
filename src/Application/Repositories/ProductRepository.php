<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class ProductRepository
{
	public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }

    public function getLineup() {
    	return $this->app->get(PDO::class)
    	->query(
    		"SELECT 
    			id, 
    			product_name, 
    			category_id, 
    			price,
		 		comment, 
		 		quantity, 
		 		production_area,
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name 
		 	 FROM product
		 	 WHERE display_flg = '1'"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
    	return $this->app->get(PDO::class)
    	->query(
    		"SELECT 
    			id, 
    			product_name, 
    			category_id, 
    			price,
		 		comment, 
		 		quantity, 
		 		production_area,
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name 
		 	 FROM product"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecommends() {
    	return $this->app->get(PDO::class)
    	->query(
    		"SELECT 
    			id, 
    			product_name, 
    			category_id, 
    			price,
		 		comment, 
		 		quantity, 
		 		production_area,
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name 
		 	 FROM product
		 	 WHERE display_flg = '1' AND recommend_flg = '1'"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
    	$db = $this->app->get(PDO::class);
    	$stmt = $db->prepare(
    		"SELECT 
    			id, 
    			product_name, 
    			category_id, 
    			price,
		 		comment, 
		 		quantity, 
		 		production_area,
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name 
		 	 FROM product
		 	 WHERE id = :id"
    	);
    	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
    	$stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAndGetRowId($product) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"INSERT INTO product (
    			product_name, 
    			category_id, 
    			price,
		 		comment, 
		 		quantity, 
		 		production_area, 
		 		recommend_flg
		 	 ) VALUES (
		 	 	:product_name,
		 	 	:category_id,
		 	 	:price,
		 	 	:comment,
		 	 	:quantity,
		 	 	:production_area,
		 	 	:recommend_flg
		 	 )"
    	);
    	$stmt->bindValue(':product_name', $product['product_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':category_id', $product['category_id'], PDO::PARAM_INT);
    	$stmt->bindValue(':price', $product['price'], PDO::PARAM_INT);
    	$stmt->bindValue(':comment', $product['comment'], PDO::PARAM_STR);
    	$stmt->bindValue(':quantity', $product['quantity'], PDO::PARAM_STR);
    	$stmt->bindValue(':production_area', $product['production_area'], PDO::PARAM_STR);
    	$stmt->bindValue(':recommend_flg', $product['recommend_flg'], PDO::PARAM_STR);
    	$stmt->execute();

        $result = $db->query("SELECT LAST_INSERT_ID()")->fetch();
        return $result[0];
    }

    public function updateImgNameById($product_id, $img_name) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"UPDATE product SET
    		 	img_name = :img_name
    		 WHERE id = :id"
    	);
    	$stmt->bindValue(':img_name', $img_name, PDO::PARAM_STR);
    	$stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function getImgNameById($product_id) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"SELECT img_name 
    		 FROM product
    		 WHERE id = :id"
    	);
    	$stmt->bindValue(':id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();

    	return ($stmt->fetch())['img_name'];
    }

    public function update($product) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"UPDATE product SET
    			product_name = :product_name,
    			category_id = :category_id,
    			price = :price,
		 		comment = :comment,
		 		quantity = :quantity,
		 		production_area = :production_area,
		 		recommend_flg = :recommend_flg
		 	 WHERE id = :id"
    	);
    	$stmt->bindValue(':product_name', $product['product_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':category_id', $product['category_id'], PDO::PARAM_INT);
    	$stmt->bindValue(':price', $product['price'], PDO::PARAM_INT);
    	$stmt->bindValue(':comment', $product['comment'], PDO::PARAM_STR);
    	$stmt->bindValue(':quantity', $product['quantity'], PDO::PARAM_STR);
    	$stmt->bindValue(':production_area', $product['production_area'], PDO::PARAM_STR);
    	$stmt->bindValue(':recommend_flg', $product['recommend_flg'], PDO::PARAM_STR);
    	$stmt->bindValue(':id', $product['id'], PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function deleteById($id) {
    	$db = $this->app->get(PDO::class);
        $stmt = $db->prepare(
    		"DELETE FROM product WHERE id = :id"
    	);
    	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
    	$stmt->execute();
    }
}