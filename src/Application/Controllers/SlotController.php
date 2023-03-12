<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Application\Controllers\BaseController;
use App\Application\Repositories\CouponRepository;
use App\Application\Repositories\GeneralRepository;
use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class SlotController extends BaseController
{
    protected CouponRepository $couponRep;
    protected GeneralRepository $generalRep;

    public function __construct(ContainerInterface $app) 
    {
        parent::__construct($app->get(LoggerInterface::class));
        $this->couponRep = $app->get(CouponRepository::class);
        $this->generalRep = $app->get(GeneralRepository::class);
    }

    public function slotPage($request, $response, $args): Response
    {
        $slotrates = $this->generalRep->getAllByKey1('slot-rate');
        $slotrates_value = array_map(function ($x) {return $x['value'];} ,$slotrates);
        $twig = Twig::create('../templates');
        $response = $twig->render($response, 'slot.html', [
            'slotrates' => implode(',', $slotrates_value)
        ]);
        return $response;
    }

   public function postCoupon($request, $response, $args): Response
   {
        $access_token = ($request->getParsedBody())['access_token'];
        $coupon_id = ($request->getParsedBody())['coupon_id'];
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
            /* ここまではしなくていいか
            $coupon = $this->couponRep->get($userId);
            if ($coupon !== false) {
                return $response->withStatus(400);
            }
            */
            $this->couponRep->delete($userId);
            $this->couponRep->insert($userId, $coupon_id);
        }

        return $response;
   }

}