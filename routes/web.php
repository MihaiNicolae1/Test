<?php

use App\Controller\ChartController;
use App\Controller\HomeController;
use \App\Controller\AccountController;
use \App\Controller\AuthController;
use App\Controller\ProductController;
use App\Controller\RegisterController;
use App\Controller\InvoiceController;
use App\Controller\UserController;

$router->controller('/', HomeController::class);
$router->controller('/account', AccountController::class);
$router->controller('/auth', AuthController::class);
$router->controller('/register', RegisterController::class);
$router->controller('/invoice', InvoiceController::class);
$router->controller('/user', UserController::class);
$router->controller('/product', ProductController::class);
$router->controller('/chart', ChartController::class);

