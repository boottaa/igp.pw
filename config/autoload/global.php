<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Zend\Log\Writer\Stream;

return [
    'isDebug' => true,
    'root_dir' => (__DIR__).'/../../',
	'db' => [
		'driver'         => 'Pdo',
		'dsn'            => 'mysql:dbname=igp;host=localhost',
		'username'       => 'bootta',
		'password'       => '1991',
		'driver_options' => [
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		],
	],

    'navigation' => require __DIR__ . '/navigation.php',

    'log' => [
        'writers' => [
            //Можем добавить несколько стримов.
            ['name' => new Stream(__DIR__.'/../../log/web.igp.wp.log'),]
        ],
    ],

    'cache' => [
        'adapter' => [
            'name' => 'memcached',
            'options' => [
                'namespace' => 'methods',
                'ttl' => 30,
                'servers' => [
                    ['127.0.0.1', 11211],
                ],
            ],
        ],
    ],
];