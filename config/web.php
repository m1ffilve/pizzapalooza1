<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'biba',
            'enableCsrfValidation' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'zbateson\yii2\mailgun\Mailer',
            'key' => '62d6527cbebbae7234d1b05ebbaa3bd4',
            'domain' => 'smtp.mailgun.org',
            // Установите true для использования API Mailgun
            'useApi' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'index' => 'site/index',
                'register' => 'site/register',
                'logout' => 'site/logout',
                'profile' => 'site/profile',
                'edit-profile' => 'site/edit-profile',
                'pizza' => 'site/pizza',
                'site/rate-pizza' => 'site/rate-pizza',
                'cart' => 'site/cart',
                'zakuski' => 'site/zakuski',
                'deserti' => 'site/deserti',
                'napitki' => 'site/napitki',
                'sousi' => 'site/sousi',
                'apply-promo' => 'site/apply-promo',
                'onas' => 'site/onas',
                'cont' => 'site/cont',
                'create-order' => 'site/create-order',
                'payment' => 'site/payment',
                'update-quantity' => 'site/update-quantity',
                'remove-item-from-cart' => 'site/remove-item-from-cart',
                'rate-pizza' => 'site/rate-pizza',
                'process_star_rating' => 'site/process_star_rating',
                'review' => 'site/review',
                'add-review' => 'site/add-review',
                'save-rating' => 'site/save-rating',
                'get-rating' => 'site/get-rating',
                'vacancy' => 'site/vacancy',
                'take-order' => 'site/take-order',
                'submit-application' => 'site/submit-application',
                'appl-answer' => 'site/appl-answer',
                'stocks' => 'site/stocks',
                'stats' => 'site/stats',
                'get-cart-item-count' => 'site/get-cart-item-count',
                'get-dishes' => 'site/get-dishes',
                'get-dish-details' => 'site/get-dish-details',
                'update-dish' => 'site/update-dish',
                'get-order-history' => 'site/get-order-history',
                'repeat-order' => 'site/repeat-order',
                'delete-order' => 'site/delete-order',
                'reply' => 'site/reply',
            ],
        ],
        
    ],
    
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
