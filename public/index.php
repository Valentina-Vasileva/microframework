<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use function App\Renderer\render;
use function App\response;

$opt = array(
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
);

$pdo = new \PDO('sqlite:../db.sqlite', null, null, $opt);
$pdo->exec('create table if not exists cars (id integer primary key autoincrement, model text not null, year text)');
$pdo->exec(
    'create table if not exists car_pictures (id integer primary key autoincrement, car_id integer not null, name text)'
);

$repository = new CarRepository($pdo);

$app = new Application();

$app->get('/', function ($params, $args, $cookies, $session) {
    $session->start();
    $nickname = $session->get('nickname');
    return response(render('index', ['nickname' => $nickname]));
});

$app->get('/companies/:id', function ($params, $args) {
    return response(json_encode($args));
});

$app->get('/setcookies', function ($params, $args, $cookies) {
    return response()->withCookie('mycookie', 'is perfect')->redirect('/companies');
});

$app->get('/companies', function ($params, $args, $cookies) {
    return response(json_encode($cookies));
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
    $pictures = [];

    if (!$car['model']) {
        $errors['model'] = "model can't be blank";
    }

    if (array_key_exists('car', $_FILES)) {
        $files = $_FILES['car'];
        $key = 'pictures';
        $errorCodes = $files['error'][$key];
        foreach ($errorCodes as $errorCode) {
            if ($errorCode !== UPLOAD_ERR_NO_FILE && $errorCode !== UPLOAD_ERR_OK) {
                $errors[$key] = 'smth wrong with pictures';
            }
        }
    }

    if (!array_key_exists($key, $errors)) {
        foreach ($files['tmp_name'][$key] as $index => $tmpName) {
            if ($files['error'][$key][$index] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $newFileName = implode(DIRECTORY_SEPARATOR, [__DIR__, 'images', basename($tmpName)]);
            if (move_uploaded_file($tmpName, $newFileName)) {
                $pictures[] = ['name' => basename($tmpName)];
            } else {
                $errors[$key] = 'smth wrong with pictures moving';
            }
        }
    }

    if (empty($errors)) {
        $repository->insert($car, $pictures);
        return response()->redirect('/cars');
    }
    return response(render('cars/new', ['errors' => $errors, 'car' => $car]))->withStatus(422);
});

$app->delete('/cars/:id', function ($params, $attributes) use ($repository) {
    $id = $attributes['id'];
    $repository->delete($id);
    return response()->redirect('/cars');
});

$app->get('/session/new', function () {
    return response(render('session/new'));
});


$app->post('/session', function ($params, $args, $cookies, $session) {
    $session->start();
    $session->set('nickname', $params['nickname']);
    return response()->redirect('/');
});

$app->delete('/session', function ($params, $args, $cookies, $session) {
    $session->destroy();
    return response()->redirect('/');
});

$app->run();
