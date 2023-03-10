<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use DateTime;
use App\Application\Controllers\BaseController;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use App\Application\Repositories\GeneralRepository;
use App\Application\Settings\SettingsInterface;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Cookies;
use Slim\Views\Twig;
use Firebase\JWT\JWT;

class AdminController extends BaseController
{

    protected ProductRepository $productRep;
    protected CategoryRepository $categoryRep;
    protected GeneralRepository $generalRep;
    protected SettingsInterface $settings;

    public function __construct(ContainerInterface $app) 
    {
        parent::__construct($app->get(LoggerInterface::class));
        $this->productRep = $app->get(ProductRepository::class);
        $this->categoryRep = $app->get(CategoryRepository::class);
        $this->generalRep = $app->get(GeneralRepository::class);
        $this->settings = $app->get(SettingsInterface::class);
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
        $correct_password = $this->generalRep->getOneByKey1('admin-password');

        $jwtSettings = $this->settings->get('jwt');
        if ($password === $correct_password) {
            $token = JWT::encode(['name' => 'wakamiya'], $jwtSettings['secret'], $jwtSettings['algorithm']);
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
                'error' => '????????????????????????????????????'
            ]);
            return $response;
        }
    }

    public function generalPage($request, $response, $args): Response
    {
        $password = $this->generalRep->getOneByKey1('admin-password');
        $tax = $this->generalRep->getOneByKey1('tax');
        $categories = $this->categoryRep->getAll();
        $slotrates = $this->generalRep->getAllByKey1('slot-rate');

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'general.html', [
            'password' => $password,
            'tax' => $tax,
            'categories' => $categories,
            'slotrates' => $slotrates
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

    public function updateSlotRate($request, $response, $args): Response
    {
        $rate = $request->getParsedBody()['rate'];
        $rate_id = $args['rate_id'];
        $this->generalRep->updateByKey1Key2('slot-rate', $rate_id, $rate);
        return $response
        ->withHeader('Location', '../general')
        ->withStatus(302);
    }

    public function updateCategory($request, $response, $args): Response
    {
        $category = $request->getParsedBody();
        $category['category_id'] = $args['category_id'];
        $this->categoryRep->update($category);
        return $response
        ->withHeader('Location', '../general')
        ->withStatus(302);
    }

    public function productsPage($request, $response, $args): Response
    {

        $products = $this->productRep->getAll();
        $recommends = $this->productRep->getRecommendAll();
        $categories = $this->categoryRep->getAll();
        $tax = $this->generalRep->getOneByKey1('tax');

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'products.html', [
            'categories' => $categories,
            'recommends' => $recommends,
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
        $img_name = $new_img_name = $this->createImgName($product_id ,$fileExt);
        $uploadedFile->moveTo('../public/static/img/tmp/' . $img_name);
        $this->productRep->updateImgNameById($product_id, $img_name);

        return $response
        ->withHeader('Location', 'products')
        ->withStatus(302);
    }

    public function updateProduct($request, $response, $args): Response
    {
        $product = $request->getParsedBody();
        $product['product_id'] = $args['product_id'];
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
        $product_id = ($request->getParsedBody())['product_id'];
        $img_name = $this->productRep->getImgNameById($product_id);
        unlink('../public/static/img/tmp/' . $img_name);

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['img'];
        $fileExt = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $new_img_name = $this->createImgName($product_id ,$fileExt);
        $uploadedFile->moveTo('../public/static/img/tmp/' . $new_img_name);
        $this->productRep->updateImgNameById($product_id, $new_img_name);
        
        return $response
        ->withHeader('Location', '../products')
        ->withStatus(302);
    }

    private function createImgName($product_id, $fileExt): string 
    {
        $objDateTime = new DateTime();
        return 'product-' . $product_id.'-'.$objDateTime->format('YmdHis').'.'.$fileExt;
    }

}