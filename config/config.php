<?php

$autoloader = new \Symfony\Component\ClassLoader\UniversalClassLoader();
$autoloader->registerNamespace('Axis\\S1\\ServiceContainer', __DIR__.'/../lib/');

$autoloader->register(true);

require_once __DIR__.'/../lib/vendor/autoload.php';