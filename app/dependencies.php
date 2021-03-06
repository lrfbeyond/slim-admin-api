<?php 
//use Respect\Validation\Validator as v;
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-token");

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/function.php';

$container = $app->getContainer();

// $container['errorHandler'] = function ($c) {
//     return function ($request, $response, $exception) use ($c) {
//         return $c['response']->withStatus(500)
//                              ->withHeader('Content-Type', 'text/html')
//                              ->write('Something went wrong!');
//     };
// };

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'text/html')
            ->write('找不到页面');
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function ($request, $response, $methods) use ($c) {
        return $c['response']
            ->withStatus(405)
            ->withHeader('Content-type', 'text/html')
            ->write('非法请求');
        };
};

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

// Register component on container
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('../templates', [
        //'cache' => '../cache/temp',
        'cache' => false,
        'debug' => true,
        'auto_reload' => false,
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $c['router'],
        $c['request']->getUri()
    ));

    return $view;
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

$container['IndexController'] = function($container) {
    return new \App\Controllers\Home\IndexController($container);
};


$container['AuthController'] = function($container) {
    return new \App\Controllers\AuthController($container);
};

$container['MemberController'] = function ($container) {
    return new \App\Controllers\MemberController($container);
};

$container['HomeController'] = function($container) {
    return new \App\Controllers\HomeController($container);
};

$container['ArticleController'] = function($container) {
    return new \App\Controllers\ArticleController($container);
};

$container['CatelogController'] = function($container) {
    return new \App\Controllers\CatelogController($container);
};

$container['CommentController'] = function ($container) {
    return new \App\Controllers\CommentController($container);
};

$container['AdminController'] = function($container) {
    return new \App\Controllers\AdminController($container);
};

$container['RoleController'] = function($container) {
    return new \App\Controllers\RoleController($container);
};


$container['LogController'] = function($container) {
    return new \App\Controllers\LogController($container);
};

$container['SettingController'] = function($container) {
    return new \App\Controllers\SettingController($container);
};

//v::with('App\\Validation\\Rules\\');