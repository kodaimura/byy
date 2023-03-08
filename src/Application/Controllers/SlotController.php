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

   public function postSlot($request, $response, $args): Response
   {
        $access_token = ($request->getParsedBody())['access_token'];
        $result = json_decode(($request->getParsedBody())['result']);
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

            $daily = $this->slotDailyRep->get($userId);
            if (!$daily) {
                return $response->withStatus(400);
            }
            $this->slotDailyRep->delete($userId);
            $this->slotDailyRep->insert($userId, $result);
        }

        return $response;
   }

}