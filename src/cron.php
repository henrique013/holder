<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:12
 */


use Dotenv\Dotenv;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
require_once 'environment.php';


/*
 * ------------------------------------------------------
 *  Args
 * ------------------------------------------------------
 */
$namespace = $argv[1];
$params = [];
foreach (array_slice($argv, 2) as $param)
{
    $param = explode('=', $param);
    $k = array_shift($param);
    $v = implode('=', $param); // caso o valor do parâmetro contenha o sinal de '='
    $params[$k] = $v;
}


/*
 * ------------------------------------------------------
 *  Constants
 * ------------------------------------------------------
 */
// script
define('APP_SCRIPT', $namespace);


/*
 * ------------------------------------------------------
 *  Logger
 * ------------------------------------------------------
 */
$lineFormatter = new LineFormatter('[%datetime%|%level_name%] %message%' . PHP_EOL, 'H:i:s');


$handle = new StreamHandler('php://stdout');
$handle->setFormatter($lineFormatter);


$handleErr = new StreamHandler('php://stderr', Logger::NOTICE, false);
$handleErr->setFormatter($lineFormatter);


$logger = new Logger(APP_SCRIPT);
$logger->pushHandler($handle);
$logger->pushHandler($handleErr);


/*
 * ------------------------------------------------------
 *  Exception Handler
 * ------------------------------------------------------
 */
set_exception_handler(
    function (Throwable $e) use ($logger) {

        /** @var $e Exception */
        $message = sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
        $message .= "\r\n";
        $message .= "Trace:";
        $message .= "\r\n";
        $message .= $e->getTraceAsString();
        $context = [];


        $logger->critical($message, $context);


        exit(255);
    }
);


/*
 * ------------------------------------------------------
 *  Script
 * ------------------------------------------------------
 */
$class = "Holder\\Cron\\{$namespace}\\Main";
/** @var $boot \Holder\Util\Cron\Boot */
$boot = new $class($logger);
$boot->run($params);