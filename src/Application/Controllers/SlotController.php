<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Controllers\BaseController;
use App\Application\Repositories\SlotDailyRepository;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class SlotController extends BaseController
{
    protected SlotDailyRepository $slotDailyRep;

    public function __construct(ContainerInterface $app) 
    {
        parent::__construct($app->get(LoggerInterface::class));
        $this->slotDailyRep = $app->get(SlotDailyRepository::class);
    }

    public function slotPage($request, $response, $args): Response
    {
        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'slot.html', []);
        return $response;
    }

   public function slot($request, $response, $args): Response
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

            $userId = "aa";
            $daily = $this->slotDailyRep->get($userId);
            $this->logger->info(json_encode($daily));
            if (!$daily) {
                return $response->withStatus(400);
            }
            $this->slotDailyRep->delete($userId);
            $result = getSlotResult() ;
            $this->slotDailyRep->insert($userId, 1);
        }
        $response->getBody()->write(json_encode(["result" => $result]));

        return $response->withHeader('Content-Type', 'application/json');
   }

   private function getSlotResult() 
   {
        return "888"
   }

}