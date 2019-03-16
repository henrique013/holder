<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:12
 */


require_once '../vendor/autoload.php';


/*
 * ------------------------------------------------------
 *  php.ini
 * ------------------------------------------------------
 */
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('default_charset', 'UTF-8');
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');
setlocale(LC_ALL, 'pt_BR.utf-8', 'Portuguese_Brazil.1252');


/*
 * ------------------------------------------------------
 *  Error Handler
 * ------------------------------------------------------
 */
set_error_handler(
    function ($code, $message, $file, $line) {

        $reporting = error_reporting();


        // error was suppressed with the @-operator OR error is not reportable
        if ((0 === $reporting) || !($code & $reporting)) return false;


        throw new ErrorException($message, $code, 1, $file, $line);
    }
);


/*
 * ------------------------------------------------------
 *  Environment
 * ------------------------------------------------------
 */
$dotenv = \Dotenv\Dotenv::create(__DIR__);
$dotenv->overload();
$dotenv->required([
    'APP_POSTGRE_HOST',
    'APP_POSTGRE_USER',
    'APP_POSTGRE_PASS',
])->notEmpty();
$dotenv->required('APP_ENV')->allowedValues([Env::DEVELOPMENT, Env::TESTING, Env::PRODUCTION]);