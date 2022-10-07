<?php
require_once __DIR__ . '/vendor/autoload.php';
const BASE_DIR = __DIR__;
use App\Services\FlashService;
use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Exception\HttpMethodNotAllowedException;
use Phroute\Phroute\Exception\HttpRouteNotFoundException;
use Phroute\Phroute\RouteParser;
use Phroute\Phroute\Dispatcher;


include_once __DIR__ . '/templates/base.html';


$router = new RouteCollector(new RouteParser);

require_once __DIR__ . '/routes/web.php';
$dispatcher = new Dispatcher($router->getData());

try {
//    set_exception_handler('\App\Services\LoggerService::Logger');
    if(!isset($_SESSION))
        session_start();
    FlashService::flash();
    $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    
} catch (HttpRouteNotFoundException $e) {
    http_response_code(404);
    require_once "templates/404.html";
    die;
    
} catch (HttpMethodNotAllowedException $e){
    http_response_code(404);
    require_once "templates/404.html";
    die;
}
echo $response;
