<?php

$host = getenv('POSTGRES_HOST') ?: 'db';
$dbname = getenv('POSTGRES_DB') ?: 'yii2basic';
$user = getenv('POSTGRES_USER') ?: 'postgres';
$password = getenv('POSTGRES_PASSWORD') ?: '';

return [
    'class' => 'yii\db\Connection',
    'dsn' => getenv('db_dsn') ?: "pgsql:host={$host};dbname={$dbname}",
    'username' => getenv('DB_USER') ?: $user,
    'password' =>  getenv('DB_PASSWORD') ?: $password,
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
            'class' => 'yii\db\pgsql\Schema',
            'defaultSchema' => 'public'
        ]
    ],
    'on afterOpen' => function($event) {
        $event->sender->createCommand("SET timezone TO 'UTC'")->execute();
    }
];