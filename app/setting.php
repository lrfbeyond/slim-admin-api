<?php 
return [
    'settings' => [
        // 是否显示错误信息
        'displayErrorDetails' => true,
        // app版本
        'appVersion' => '2.0',
        // 安全密钥
        'safekey' => 'helloweba.com7s6dLo01sdh12o0',
        // 数据库连接配置
        'db' => [
            'host' => 'localhost',
            'user' => 'root',
            'password' => '',
            'dbname' => 'slimadmin',
            'dbport' => 3306,
            'prefix' => 'hw_',
        ],
        // 日志配置
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../logs/app-'.date('Y-m-d').'.log',
        ],
    ]
];
