<?php 
//use Respect\Validation\Validator as v;
session_start();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/function.php';

$container = $app->getContainer();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$container['safekey'] = function ($c) {
    $settings = $c->get('settings');
    return $settings['safekey'];
};

$container['logger'] = function($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $file_handler = new \Monolog\Handler\StreamHandler($settings['logger']['path']);
    $logger->pushHandler($file_handler);
    return $logger;
};

// $container['db'] = function ($c) {
//     $db = $c['settings']['db'];
//     $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
//         $db['user'], $db['pass']);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//     return $pdo;
// };

// $container[App\Controller\HomeController::class] = function ($c) {
//     return new App\Controller\HomeController($c);
// };

$container['UserController'] = function ($container) {
    return new \App\Controllers\UserController($container);
};

$container['HomeController'] = function($container) {
    return new \App\Controllers\HomeController($container);
};

$container['ArticleController'] = function($container) {
    return new \App\Controllers\ArticleController($container);
};

$container['AuthController'] = function($container) {
    return new \App\Controllers\AuthController($container);
};

//v::with('App\\Validation\\Rules\\');