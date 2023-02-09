<?php

declare(strict_types=1);

use App\Application\Controllers\ProductController;
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

        //$grp->get('/admin', ListUsersAction::class);

        $grp->group('/products', function (Group $grp1) {
            $grp1->get('', ProductController::class. ':productsPage');
            $grp1->post('', ProductController::class. ':registerProduct');
            $grp1->post('/img', ProductController::class. ':updateImg');
            $grp1->post('/{product_id}', ProductController::class. ':updateProduct');
            $grp1->delete('/{product_id}', ProductController::class. ':deleteProduct');
        });
    });
};