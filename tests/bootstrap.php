<?php

$srcDir = realpath(__DIR__ . '/../src/');

set_include_path($srcDir . PATH_SEPARATOR . get_include_path());

spl_autoload_register(function ($class) {
    if(strpos($class, '\\') !== false) {
        include str_replace("\\", "/", $class) . '.php';
    }
});