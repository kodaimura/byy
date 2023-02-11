<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use \PDO;
use Psr\Log\LoggerInterface;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use App\Application\Repositories\CustomerRepository;
use App\Application\Repositories\OrderRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class CustomerController
{
    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
        $this->logger = $app->get(LoggerInterface::class);
        $this->productRep = $app->get(ProductRepository::class);
        $this->categoryRep = $app->get(CategoryRepository::class);
        $this->customerRep = $app->get(CustomerRepository::class);
        $this->orderRep = $app->get(OrderRepository::class);
    }

    public function lineupPage($request, $response, $args): Response
    {
        $products = $this->productRep->getLineup();
        $recommends = $this->productRep->getRecommends();
        $categories = $this->categoryRep->getAll();

        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'lineup.html', [
            'categories' => $categories,
            'products' => $products,
            'recommends' => $recommends
        ]);
        return $response;
    }

   public function order($request, $response, $args): Response
   {
        $access_token = ($request->getParsedBody())['access_token'];
        $orders = json_decode(($request->getParsedBody())['order']);
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
                    'product_id' => $order->{'id'},
                    'count' => $order->{'order_quantity'}
                ]);
                $total_price += intval($order->{'order_quantity'}) * intval($order->{'price'});
            }
            
            $this->customerRep->upsert([
                'customer_id' => $userId,
                'customer_name' => $userName,
                'visits_count' => 1,
                'cumulative_payment' => $total_price
            ]);
        }

        return $response;
   }

}