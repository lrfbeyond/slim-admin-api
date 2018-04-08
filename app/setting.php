<?php 
return [
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'dbname' => 'hellowebanet',
            'dbport' => 3306,
            'prefix' => 'hw_',
        ],
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../logs/app-'.date('Y-m-d').'.log',
        ],
    ]
];
