<?php

namespace App;

class Application implements ApplicationInterface
{
    private $handlers = [];

    public function get($path, $handler)
    {
        $this->handlers[] = ['GET', $path, $handler];
    }

    public function post($path, $handler)
    {
        $this->handlers[] = ['POST', $path, $handler];
    }

    public function run()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->handlers as $item) {
            [$handlerMethod, $path, $handler] = $item;
            $preparedPath = preg_quote($path, '/');
            if ($method === $handlerMethod && preg_match("/^$preparedPath$/i", $uri)) {
                echo $handler();
                return;
            }
        }
        echo 'Not found';
        return;
    }
}
