<?php
// Environment Variables

use Dotenv\Dotenv;


$dotenv = Dotenv::create(realpath(__DIR__ . '/../'));
$dotenv->overload();
$dotenv->required([
    'APP_POSTGRE_HOST',
    'APP_POSTGRE_USER',
    'APP_POSTGRE_PASS',
    'APP_POSTGRE_DB_DEFAULT',
])->notEmpty();