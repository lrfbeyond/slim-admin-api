<?php
//use \Psr\Http\Message\ServerRequestInterface as Request;
//use \Psr\Http\Message\ResponseInterface as Response;
date_default_timezone_set("PRC");

require '../vendor/autoload.php';

$settings = require '../app/setting.php';

$app = new \Slim\App($settings);

require '../app/dependencies.php';

require '../app/routes.php';

$app->run();
