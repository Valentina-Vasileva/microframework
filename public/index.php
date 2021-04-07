<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function App\Renderer\render;

$app = new Application();

$app->get('/', function () {
    return render('index');
});

$app->get('/companies', function () {
    return 'companies list';
});

$app->get('/about', function ($params) {
    return json_encode($params);
});

$app->run();
