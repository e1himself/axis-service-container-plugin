<?php

$autoloader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$autoloader->registerNamespace('Axis\\S1\\ServiceContainer', __DIR__.'/../lib/');

$vendorDir = __DIR__.'/../lib/vendor';
$baseDir = dirname(dirname($vendorDir));

$map = require $vendorDir . '/composer/autoload_namespaces.php';
$autoloader->registerNamespaces($map);
$autoloader->register(true);

$classMap = require $vendorDir . '/composer/autoload_classmap.php';
$autoloader = new \Symfony\Component\ClassLoader\MapClassLoader($classMap);
$autoloader->register(true);