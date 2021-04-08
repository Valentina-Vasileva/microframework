<?php

namespace App;

class CarRepository
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare('delete from cars where id = ?');
        return $stmt->execute([$id]);
    }

    public function all()
    {
        $cars = $this->pdo->query('select * from cars')->fetchAll();
        $pictures = $this->pdo->query('select car_id, id, name from car_pictures')->fetchAll(\PDO::FETCH_GROUP);

        $carsWithPictures = array_map(function ($car) use ($pictures) {
            if (array_key_exists($car['id'], $pictures)) {
                $car['pictures'] = $pictures[$car['id']];
            } else {
                $car['pictures'] = [];
            }
            return $car;
        }, $cars);

        return $carsWithPictures;
    }

    public function insert($carParams, $pictures)
    {
        $pdo = $this->pdo;

        $fields = implode(', ', array_keys($carParams));
        $values = implode(', ', array_map(function ($v) use ($pdo) {
            return $pdo->quote($v);
        }, array_values($carParams)));
        $pdo->exec("insert into cars ($fields) values ($values)");
        $carId = $pdo->lastInsertId();

        foreach ($pictures as $picture) {
            $picture['car_id'] = $carId;
            $fields = implode(', ', array_keys($picture));
            $values = implode(', ', array_map(function ($v) use ($pdo) {
                return $pdo->quote($v);
            }, array_values($picture)));
            $pdo->exec("insert into car_pictures ($fields) values ($values)");
        }
    }
}
