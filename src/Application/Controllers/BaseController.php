<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use Psr\Log\LoggerInterface;
use Psr\Container\ContainerInterface;
use App\Application\Repositories\ProductRepository;
use App\Application\Repositories\CategoryRepository;
use App\Application\Repositories\CustomerRepository;
use App\Application\Repositories\OrderRepository;
use App\Application\Repositories\GeneralRepository;

class BaseController
{
    protected ContainerInterface $app;

    protected LoggerInterface $logger;

    protected ProductRepository $productRep;
    protected CategoryRepository $categoryRep;
    protected GeneralRepository $generalRep;
    protected CustomerRepository $customerRep;
    protected OrderRepository $orderRep;

    public function __construct(ContainerInterface $app, LoggerInterface $logger)
    {
        $this->app = $app;
        $this->logger = $logger;
    }
}