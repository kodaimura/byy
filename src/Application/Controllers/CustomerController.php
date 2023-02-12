<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Controllers\BaseController;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use App\Application\Repositories\CustomerRepository;
use App\Application\Repositories\OrderHistoryRepository;
use App\Application\Repositories\GeneralRepository;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class CustomerController extends BaseController
{
    protected ProductRepository $productRep;
    protected CategoryRepository $categoryRep;
    protected GeneralRepository $generalRep;
    protected CustomerRepository $customerRep;
    protected OrderHistoryRepository $orderRep;

    public function __construct(ContainerInterface $app) 
    {
        parent::__construct($app->get(LoggerInterface::class));
        $this->productRep = $app->get(ProductRepository::class);
        $this->categoryRep = $app->get(CategoryRepository::class);
        $this->customerRep = $app->get(CustomerRepository::class);
        $this->orderRep = $app->get(OrderHistoryRepository::class);
        $this->generalRep = $app->get(GeneralRepository::class);
    }

    public function lineupPage($request, $response, $args): Response
    {
        $products = $this->productRep->getLineup();
        $recommends = $this->productRep->getRecommends();
        $categories = $this->categoryRep->getAll();
        $tax = $this->generalRep->getOneByKey1('tax');

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'lineup.html', [
            'categories' => $categories,
            'products' => $products,
            'recommends' => $recommends,
            'tax_rate' => (1 + intval($tax) / 100)
        ]);
        return $response;
    }

   public function order($request, $response, $args): Response
   {
        $access_token = ($request->getParsedBody())['access_token'];
        $orders = json_decode(($request->getParsedBody())['orders']);
        $header = [('Authorization: Bearer ' . $access_token)];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/profile');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); 
        $res = json_decode(curl_exec($curl), true);
        $errno = curl_errno($curl);
        curl_close($curl);

        if ($errno === CURLE_OK) {
            $userId = $res['userId'];
            $userName = $res['displayName'];

            $total_price = 0;
            foreach ($orders as $order) {
                $this->orderRep->upsert([
                    'customer_id' => $userId,
                    'product_id' => $order->{'product_id'},
                    'order_count' => $order->{'order_count'}
                ]);
                $total_price += intval($order->{'order_count'}) * intval($order->{'unit_price'});
            }
            
            $this->customerRep->upsert([
                'customer_id' => $userId,
                'customer_name' => $userName,
                'visit_count' => 1,
                'total_payment' => $total_price
            ]);
        }

        return $response;
   }

}