<?php

declare(strict_types=1);

use App\Application\Controllers\AdminController;
use App\Application\Controllers\CustomerController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/wakamiya', function (Group $grp) {
        $grp->get('', CustomerController::class. ':lineupPage');
        $grp->post('/orders', CustomerController::class. ':order');

        $grp->get('/login', AdminController::class. ':loginPage');
        $grp->post('/login', AdminController::class. ':login');

        $grp->group('/admin', function (Group $grp1) {
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
