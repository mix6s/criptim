<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$env = $_SERVER['ENV'] ?? 'prod';
$appType = $_SERVER['APP_TYPE'] ?? 'console';
if ($env == 'dev') {
	Debug::enable();
}
$kernel = new AppKernel($env, $env == 'dev', $appType);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
