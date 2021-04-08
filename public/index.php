<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function App\Renderer\render;
use function App\response;

$opt = array(
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
);

$pdo = new \PDO('sqlite:db.sqlite', null, null, $opt);

$pdo->exec('create table if not exists cars (id integer primary key autoincrement, model text not null, year text)');

$repository = new CarRepository($pdo);

$app = new Application();

$app->get('/', function () {
    return response(render('index'));
});

$app->get('/companies/:id', function ($params, $args) {
    return response(json_encode($args));
});

$app->get('/companies', function () {
    return response('companies list');
});

$app->get('/about', function ($params) {
    return response(json_encode($params));
});

$newCar = [
    'model' => '',
    'year' => ''
];

$app->get('/cars', function () use ($repository) {
    $cars = $repository->all();
    return response(render('cars/index', ['cars' => $cars]));
});

$newCar = [
    'model' => '',
    'year' => ''
];

$app->get('/cars/new', function () use ($newCar) {
    return response(render('cars/new', ['errors' => [], 'car' => $newCar]));
});

$app->post('/cars', function ($params, $attributes) use ($repository) {
    $car = $params['car'];
    $errors = [];

    if (!$car['model']) {
        $errors['model'] = "model can't be blank";
    }
    if (!$car['year']) {
        $errors['year'] = "year can't be blank";
    }

    if (empty($errors)) {
        $repository->insert($car);
        return response()->redirect('/cars');
    }
    return response(render('cars/new', ['errors' => $errors, 'car' => $car]))->withStatus(422);
});

$app->delete('/cars/:id', function ($params, $attributes) use ($repository) {
    $id = $attributes['id'];
    $repository->delete($id);
    return response()->redirect('/cars');
});

$app->run();
