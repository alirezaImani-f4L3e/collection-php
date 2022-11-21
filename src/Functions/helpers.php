<?php

use RSO\Collection\Managers\CollectionManager;


if (!function_exists('collection')) {
    /**
     *
     * @return CollectionManager
     */
    function collection($config = [] , $environment = "production")
    {
        return (new CollectionManager($config , $environment))->getCollection();
    } 
}

function collection_config($key, $default = null)
{
    $config = require __DIR__ . "/../config/config.php";
    return isset($config[$key]) ? $config[$key] : $default;
}
