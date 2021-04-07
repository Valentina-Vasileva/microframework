<?php

namespace App\Renderer;

function render($path, $variables = [])
{
    $parts = [__DIR__, '..', 'resources', 'views', $path . '.phtml'];
    $fullPath = implode(DIRECTORY_SEPARATOR, $parts);
    return \App\Template\render($fullPath, $variables);
}
