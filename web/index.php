<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';
if (PHP_VERSION_ID < 70000) {
	include_once __DIR__ . '/../var/bootstrap.php.cache';
}

$env = $_SERVER['SERVER_DEV_TYPE'] ?? 'prod';
if ($env == 'dev') {
	Debug::enable();
}
$kernel = new AppKernel($env, $env == 'dev');
if (PHP_VERSION_ID < 70000) {
	$kernel->loadClassCache();
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
