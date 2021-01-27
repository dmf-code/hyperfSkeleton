<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

if (!function_exists("api_resource")) {
    function api_resource($path, $controller)
    {
        Router::get($path,"$controller@index");
        Router::post($path,"$controller@store");
        Router::get("$path/{id}","$controller@show");
        Router::put("$path/{id}", "$controller@update");
        Router::delete("$path/{id}","$controller@destroy");
    }
}

include_once __DIR__ .'/subRoutes/admin.php';
include_once __DIR__ .'/subRoutes/front.php';

