<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application();

$app->get('/', function () {
    return 'smth';
});

$app->get('/companies', function () {
    return 'companies list';
});

$app->run();
