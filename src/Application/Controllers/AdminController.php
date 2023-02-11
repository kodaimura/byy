<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use \PDO;
use Psr\Log\LoggerInterface;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use App\Application\Repositories\GeneralRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Cookies;
use Slim\Views\Twig;
use Firebase\JWT\JWT;

class AdminController
{
    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->logger = $app->get(LoggerInterface::class);
        $this->productRep = $app->get(ProductRepository::class);
        $this->categoryRep = $app->get(CategoryRepository::class);
        $this->generalRep = $app->get(GeneralRepository::class);
    }

    public function loginPage($request, $response, $args): Response
    {
        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'login.html', []);
        return $response;
    }

    public function login($request, $response, $args): Response
    {
        $password = $request->getParsedBody()['password'];
        if ($password === 'admin') {
            $token = JWT::encode(['name' => 'wakamiya'], 'supersecretkeyyoushouldnotcommittogithub', 'HS256');
            $cookies = (new Cookies())
            ->set('token', [
                'value'   => $token,
                'path'    => '/',
                'expires' => time() + 30 * 24 * 3600,
            ]);
            return $response
            ->withHeader('Location', 'admin/products')
            ->withHeader('Set-Cookie', $cookies->toHeaders())
            ->withStatus(302);

        } else {
            $twig = Twig::create('../templates');
            $response = $twig->render($response, 'login.html', [
                'error' => 'パスワードが異なります。'
            ]);
            return $response;
        }
    }

    public function generalPage($request, $response, $args): Response
    {
        $password = $this->generalRep->getOneByKey1('admin-password');
        $tax = $this->generalRep->getOneByKey1('tax');
        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'general.html', [
            'password' => $password,
            'tax' => $tax
        ]);
        return $response;
    }

    public function updatePassword($request, $response, $args): Response
    {
        $password = $request->getParsedBody()['password'];
        $this->generalRep->updateByKey1('admin-password', $password);
        return $response;

    }

    public function updateTax($request, $response, $args): Response
    {
        $tax = $request->getParsedBody()['tax'];
        $this->generalRep->updateByKey1('tax', $tax);
        return $response;
    }

    public function productsPage($request, $response, $args): Response
    {

        $products = $this->productRep->getAll();
        $categories = $this->categoryRep->getAll();
        $tax = $this->generalRep->getOneByKey1('tax');

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'products.html', [
            'categories' => $categories,
            'products' => $products,
            'tax_rate' => (1 + intval($tax)/100)
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