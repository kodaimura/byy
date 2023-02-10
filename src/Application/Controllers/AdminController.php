<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use \PDO;
use Psr\Log\LoggerInterface;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class AdminController
{
    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->logger = $app->get(LoggerInterface::class);
        $this->productRep = $app->get(ProductRepository::class);
        $this->categoryRep = $app->get(CategoryRepository::class);
    }

    public function productsPage($request, $response, $args): Response
    {

        $products = $this->productRep->getAll();
        $categories = $this->categoryRep->getAll();

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'products.html', [
            'categories' => $categories,
            'products' => $products
        ]);
        return $response;
    }

    public function registerProduct($request, $response, $args): Response
    {
        $product_id = $this->productRep->insertAndGetRowId($request->getParsedBody());
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['img'];
        $fileExt = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = 'product-' . $product_id.'.'.$fileExt;
        $uploadedFile->moveTo('../public/static/img/tmp/' . $filename);
        $this->productRep->updateImgNameById($product_id, $filename);

        return $response
        ->withHeader('Location', 'products')
        ->withStatus(302);
    }

    public function updateProduct($request, $response, $args): Response
    {
        $product = $request->getParsedBody();
        $product['id'] = $args['product_id'];
        $this->productRep->update($product);
        
        return $response;
    }

    public function deleteProduct($request, $response, $args): Response
    {
        $product_id = $args['product_id'];
        $img_name = $this->productRep->getImgNameById($product_id);
        $this->productRep->deleteById($product_id);
        unlink('../public/static/img/tmp/' . $img_name);

        return $response;
    }

    public function updateImg($request, $response, $args): Response
    {
        $product_id = ($request->getParsedBody())['id'];
        $img_name = $this->productRep->getImgNameById($product_id);
        unlink('../public/static/img/tmp/' . $img_name);

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['img'];
        $fileExt = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = 'product-' . $product_id.'.'.$fileExt;
        $uploadedFile->moveTo('../public/static/img/tmp/' . $filename);
        $this->productRep->updateImgNameById($product_id, $filename);
        
        return $response
        ->withHeader('Location', '/wakamiya/products')
        ->withStatus(302);
    }

}