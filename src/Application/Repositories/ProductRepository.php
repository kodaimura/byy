<?php

declare(strict_types=1);

namespace App\Application\Repositories;

use \PDO;
use Psr\Container\ContainerInterface;

class ProductRepository extends BaseRepository
{

    public function getLineup() {
    	return $this->db
    	->query(
    		"SELECT 
    			product_id, 
    			category_id,
    			product_name, 
    			production_area,
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		stock_flg, 
		 		img_name
		 	 FROM product
		 	 WHERE display_flg = '1'
		 	 ORDER BY seq"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
    	return $this->db
    	->query(
    		"SELECT 
    			product_id, 
    			category_id,
    			product_name, 
    			production_area,
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name,
		 		seq
		 	 FROM product
		 	 ORDER BY seq"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecommends() {
    	return $this->db
    	->query(
    		"SELECT 
    			product_id, 
    			category_id,
    			product_name, 
    			production_area,
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		stock_flg, 
		 		img_name
		 	 FROM product
		 	 WHERE display_flg = '1' AND recommend_flg = '1'
		 	 ORDER BY seq"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecommendAll() {
    	return $this->db
    	->query(
    		"SELECT 
    			product_id, 
    			category_id,
    			product_name, 
    			production_area,
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name,
		 		seq
		 	 FROM product
		 	 WHERE recommend_flg = '1'
		 	 ORDER BY seq"
    	)
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($product_id) {
    	$stmt = $this->db->prepare(
    		"SELECT 
    			product_id, 
    			category_id,
    			product_name, 
    			production_area,
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		display_flg, 
		 		stock_flg, 
		 		recommend_flg, 
		 		img_name,
		 		seq
		 	 FROM product
		 	 WHERE product_id = :product_id"
    	);
    	$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAndGetRowId($product) {
        $stmt = $this->db->prepare(
    		"INSERT INTO product (
    			category_id, 
    			product_name, 
    			production_area, 
    			unit_quantity, 
    			unit_price,
		 		comment, 
		 		recommend_flg
		 	 ) VALUES (
		 	 	:category_id,
		 	 	:product_name,
		 	 	:production_area,
		 	 	:unit_quantity,
		 	 	:unit_price,
		 	 	:comment,
		 	 	:recommend_flg
		 	 )"
    	);
    	$stmt->bindValue(':category_id', $product['category_id'], PDO::PARAM_INT);
    	$stmt->bindValue(':product_name', $product['product_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':production_area', $product['production_area'], PDO::PARAM_STR);
    	$stmt->bindValue(':unit_quantity', $product['unit_quantity'], PDO::PARAM_STR);
    	$stmt->bindValue(':unit_price', $product['unit_price'], PDO::PARAM_INT);
    	$stmt->bindValue(':comment', $product['comment'], PDO::PARAM_STR);
    	$stmt->bindValue(':recommend_flg', $product['recommend_flg'], PDO::PARAM_STR);
    	$stmt->execute();

        $result = $this->db->query("SELECT LAST_INSERT_ID()")->fetch();
        return $result[0];
    }

    public function updateImgNameById($product_id, $img_name) {
        $stmt = $this->db->prepare(
    		"UPDATE product SET
    		 	img_name = :img_name
    		 WHERE product_id = :product_id"
    	);
    	$stmt->bindValue(':img_name', $img_name, PDO::PARAM_STR);
    	$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function getImgNameById($product_id) {
    	$stmt = $this->db->prepare(
    		"SELECT img_name 
    		 FROM product
    		 WHERE product_id = :product_id"
    	);
    	$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();

    	return ($stmt->fetch(PDO::FETCH_ASSOC))['img_name'];
    }

    public function update($product) {
    	$stmt = $this->db->prepare(
    		"UPDATE product SET
    			category_id = :category_id,
    			product_name = :product_name,
    			production_area = :production_area,
    			unit_quantity = :unit_quantity,
    			unit_price = :unit_price,
		 		comment = :comment,
		 		display_flg = :display_flg,
		 		stock_flg = :stock_flg,
		 		recommend_flg = :recommend_flg,
		 		seq = :seq
		 	 WHERE product_id = :product_id"
    	);
    	$stmt->bindValue(':category_id', $product['category_id'], PDO::PARAM_INT);
    	$stmt->bindValue(':product_name', $product['product_name'], PDO::PARAM_STR);
    	$stmt->bindValue(':production_area', $product['production_area'], PDO::PARAM_STR);
    	$stmt->bindValue(':unit_quantity', $product['unit_quantity'], PDO::PARAM_STR);
    	$stmt->bindValue(':unit_price', $product['unit_price'], PDO::PARAM_INT);
    	$stmt->bindValue(':comment', $product['comment'], PDO::PARAM_STR);
    	$stmt->bindValue(':display_flg', $product['display_flg'], PDO::PARAM_STR);
    	$stmt->bindValue(':stock_flg', $product['stock_flg'], PDO::PARAM_STR);
    	$stmt->bindValue(':recommend_flg', $product['recommend_flg'], PDO::PARAM_STR);
    	$stmt->bindValue(':seq', $product['seq'], PDO::PARAM_INT);
    	$stmt->bindValue(':product_id', $product['product_id'], PDO::PARAM_INT);
    	$stmt->execute();
    }

    public function deleteById($product_id) {
    	$stmt = $this->db->prepare(
    		"DELETE FROM product WHERE product_id = :product_id"
    	);
    	$stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
    	$stmt->execute();
    }
}