<?php 
$capsule = new \Illuminate\Database\Capsule\Manager;
$db = $settings['settings']['db'];

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $db['host'],
    'database' => $db['dbname'],
    'username' => $db['user'],
    'password' => $db['password'],
    'charset' => 'utf8',
    'port' => $db['dbport'],
    'collation' => 'utf8_unicode_ci',
    'prefix' => $db['prefix'],
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();
