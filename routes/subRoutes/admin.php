<?php
declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

Router::addGroup('/admin', function () {

    Router::get('/test', function () {
        return 'api/admin/test';
    }, [
        'middleware' => []
    ]);

});

