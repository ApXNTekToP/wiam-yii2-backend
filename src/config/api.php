<?php
// config/api.php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'api-app',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\controllers\api',
    'components' => [
        'request' => [
            'cookieValidationKey' => 'wXwNB2JgMUp_EsEUCMBhEKDaz5LM1P60',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST api/v1/requests' => 'v1/my-custom-rest/requests',
                'GET api/v1/processor' => 'v1/my-custom-rest/processor',
            ],
        ],
    ],
    'params' => $params,
];