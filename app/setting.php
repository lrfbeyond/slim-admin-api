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
        //  后台自定义设置参数
        'customer' => [
            // 邮件设置
            'mailerEnable' => true,
            'mailserver' => 'smtp.163.com',
            'mailport' => '25',
            'mailuser' => 'helloweba@163.com',
            'mailpass' => '',
            // 评论开关
            'commentEnable' => true,
            'needCheck' => true,
            // 会员注册开关
            'regEnable' => true,
            // 违禁词语
            'badword' => '傻逼|SB',
        ],
    ]
];
