<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function App\Renderer\render;
use function App\response;

$app = new Application();

$app->get('/', function () {
    return response(render('index'));
});

$app->get('/companies/:id', function ($params, $args) {
    return json_encode($args);
});

$app->get('/companies', function () {
    return 'companies list';
});

$app->get('/about', function ($params) {
    return json_encode($params);
});

$app->run();
