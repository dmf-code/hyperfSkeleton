<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/front', function () {

    Router::get('/test', function () {
        return 'api/front/test';
    }, [
        'middleware' => []
    ]);

});