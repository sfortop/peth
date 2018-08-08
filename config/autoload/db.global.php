<?php
/**
 * peth
 *
 * @author Serhii Borodai <clarifying@gmail.com>
 */

$password = getenv('MYSQL_PASSWORD');
$user = getenv('MYSQL_USER');
$database = getenv('MYSQL_DATABASE');
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT');

return [
    'db' => [
        'driver'   => 'Pdo_Mysql',
        'dsn'      => sprintf('mysql:host=%s;port=%s;dbname=%s;', $host, $port, $database),
        'user'     => $user,
        'password' => $password,
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ],
    ],
    'dependencies' => [
        'factories' => [
        ],
    ],
];