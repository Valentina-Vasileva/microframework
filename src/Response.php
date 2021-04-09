<?php

namespace App;

function response($body = null)
{
    return new Response($body);
}

class Response implements ResponseInterface
{
    private $body;
    private $headers = [];
    private $statusCode = 200;
    private $cookies = [];

    public function __construct($body)
    {
        if (is_string($body)) {
            $this->headers['Content-Length'] = mb_strlen($body);
        }
        $this->body = $body;
    }

    public function redirect($url)
    {
        $this->status = 302;
        $this->headers['Location'] = $url;
        return $this;
    }

    public function withStatus($status)
    {
        $this->statusCode = $status;
        return $this;
    }

    public function withCookie($key, $value)
    {
        $this->cookies[$key] = $value;
        return $this;
    }

    public function format($format)
    {
        $this->headers['Content-Type'] = $format;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getHeaderLines()
    {
        $headers = array_map(function ($key, $value) {
            return "{$key}: {$value}";
        }, array_keys($this->headers), $this->headers);
        return $headers;
    }
}
