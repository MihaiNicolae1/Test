<?php

namespace App\Controller;
class BaseController
{
    public static function getRepositoryByClass($class)
    {
        $class = basename($class);
        $repo = "App\\Repository\\" . $class . "Repository";
        return new $repo;
    }
}
