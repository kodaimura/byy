<?php

declare(strict_types=1);

use App\Application\Controllers\AdminController;
use App\Application\Controllers\CustomerController;
use App\Application\Controllers\SlotController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/wakamiya', function (Group $grp) {
        $grp->get('', CustomerController::class. ':lineupPage');
        $grp->post('/coupon', CustomerController::class. ':getCoupon');
        $grp->post('/orders', CustomerController::class. ':order');
        $grp->get('/slot', SlotController::class. ':slotPage');
        $grp->post('/coupons', SlotController::class. ':postCoupon');

        $grp->get('/login', AdminController::class. ':loginPage');
        $grp->post('/login', AdminController::class. ':login');

        $grp->group('/admin', function (Group $grp1) {
            $grp1->get('/general', AdminController::class. ':generalPage');
            $grp1->post('/tax', AdminController::class. ':updateTax');
            $grp1->post('/password', AdminController::class. ':updatePassword');
            $grp1->post('/slotrates/{rate_id}', AdminController::class. ':updateSlotRate');
            $grp1->post('/categories/{category_id}', AdminController::class. ':updateCategory');

            $grp1->group('/products', function(Group $grp2) {
                $grp2->get('', AdminController::class. ':productsPage');
                $grp2->post('', AdminController::class. ':registerProduct');
                $grp2->post('/img', AdminController::class. ':updateImg');
                $grp2->post('/{product_id}', AdminController::class. ':updateProduct');
                $grp2->delete('/{product_id}', AdminController::class. ':deleteProduct');
            });
        });
    });
};
