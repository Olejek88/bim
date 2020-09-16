<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2advanced',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
    'modules' => [
        'politer' => [
            'class' => 'common\datasource\politer\Module',
            'controllerMap' => [
                'default' => 'common\datasource\politer\controllers\PoliterController'
            ],
            'description' => 'База Политер',
            'oracle' => [
                'class' => 'neconix\yii2oci8\Oci8Connection',
                'dsn' => 'oci:dbname=;charset=UTF8;',
                'username' => '',
                'password' => '',
                'attributes' => [PDO::ATTR_PERSISTENT => true],
                'enableSchemaCache' => true, // Oracle dictionaries is too slow :(, enable caching
                'schemaCacheDuration' => 60 * 60, // 1 hour
                'on afterOpen' => function ($event) {
                    // специфическая инициализация в конкретной базе
                    /** @var neconix\yii2oci8\Oci8Connection $db */
                    $db = $event->sender;
                    $q = /** @lang oracle sql */
                        'select PTER_LINK_API.Login(:login, :pass) AS "UserId" FROM dual';
                    $cmd = $db->createCommand($q, [':login' => '', ':pass' => '']);
                    $cmd->execute();

//                /* A session configuration example */
//                $q = <<<SQL
//begin
//  execute immediate 'alter session set NLS_SORT=BINARY_CI';
//  execute immediate 'alter session set NLS_TERRITORY=AMERICA';
//end;
//SQL;
//                $event->sender->createCommand($q)->execute();
                },
            ],
        ],
        'vega' => [
            'class' => 'common\datasource\vega\Module',
            'controllerMap' => [
                'default' => 'common\datasource\vega\controllers\VegaController'
            ],
            'description' => 'LoraWan Политер',
            'server' => [
                'host' => 'ws://127.0.0.1:8002',
                'login' => '',
                'password' => '',
            ],
        ],
    ],
];
