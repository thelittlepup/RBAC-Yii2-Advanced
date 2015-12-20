<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        // Add authManager - Opened DbManager
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        // End authManager
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
];
