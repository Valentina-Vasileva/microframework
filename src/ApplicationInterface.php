<?php

namespace App;

interface ApplicationInterface
{
    public function get($path, $handler);
    public function post($path, $handler);
    public function run();
}
