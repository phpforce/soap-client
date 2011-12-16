<?php

require_once $_SERVER['SYMFONY'] . '/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

spl_autoload_register(function($class) {
    if (0 === (strpos($class, 'Ddeboer\\Salesforce\\ClientBundle'))) {
        $path = __DIR__.'/../'.implode('/', array_slice(explode('\\', $class), 3)).'.php';

        if (!stream_resolve_include_path($path)) {
            return false;
        }
        require_once $path;
        return true;
    }
});

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => $_SERVER['SYMFONY'],
    'BeSimple\\SoapCommon' => $_SERVER['BESIMPLE_SOAP_COMMON'],
    'BeSimple\\SoapClient' => $_SERVER['BESIMPLE_SOAP_CLIENT'],
    'BeSimple\\SoapBundle' => $_SERVER['BESIMPLE_SOAP_BUNDLE']
));
$loader->register();
