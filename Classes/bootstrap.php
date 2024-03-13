<?php

include_once 'Sistema/db.php';
include_once 'Mongo/Mongo.php';
spl_autoload_register(function ($classe) {
    $arquivo = (string) str_replace('\\', '/', __DIR__ . DIRECTORY_SEPARATOR . $classe . '.php');
    if (is_readable($arquivo) === true) {
        include_once $arquivo;
    }
});
