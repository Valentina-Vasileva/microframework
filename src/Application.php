<?php

namespace App;

use App\Session;

class Application implements ApplicationInterface
{
    private $handlers = [];

    public function get($path, $handler)
    {
        $this->append('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->append('POST', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->append('DELETE', $path, $handler);
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($_SERVER['REQUEST_METHOD'] === "POST" && array_key_exists('_method', $_POST)) {
            $method = strtoupper($_POST['_method']);
        } else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        foreach ($this->handlers as $item) {
            [$handlerMethod, $path, $handler] = $item;
            $preparedPath = str_replace('/', '\/', $path);
            $matches = [];
            if ($method === $handlerMethod && preg_match("/^$preparedPath$/i", $uri, $matches)) {
                $arguments = array_filter($matches, function ($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);

                $meta = [
                    'method' => $method,
                    'uri' => $uri,
                    'headers' => getallheaders()
                ];
                $session = new Session();
                $response = $handler(array_merge($_GET, $_POST), $arguments, $_COOKIE, $session, $meta);
                http_response_code($response->getStatusCode());
                foreach ($response->getHeaderLines() as $header) {
                    header($header);
                }
                foreach ($response->getCookies() as $key => $value) {
                    setcookie($key, $value);
                }
                echo $response->getBody();
                return;
            }
        }
        echo 'Not found';
        return;
    }

    private function append($method, $path, $handler)
    {
        $updatedPath = $path;
        $matches = [];
        if (preg_match_all('/:([\w-]+)/', $path, $matches)) {
            $updatedPath = array_reduce($matches[1], function ($acc, $value) {
                $group = "(?P<$value>[\w-]+)";
                return str_replace(":{$value}", $group, $acc);
            }, $path);
        }
        $this->handlers[] = [$method, $updatedPath, $handler];
    }
}
