<?php

$loader = require __DIR__. '/../vendor/.composer/autoload.php';

spl_autoload_register(function($class) {
    if (0 === strpos($class, 'Ddeboer\\Salesforce\\ClientBundle\\')) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';
        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});