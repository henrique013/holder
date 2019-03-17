<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:32
 */

namespace Holder\Util;


use Holder\Util\DB\Postgre;
use PDO;

class Helper
{
    private function __construct() { }


    public static function connectPostgre(string $db = null, string $schema = null, string $host = null, string $user = null, string $pass = null): Postgre
    {
        $db = $db ?: $_ENV['APP_POSTGRE_DB_DEFAULT'];
        $host = $host ?: $_ENV['APP_POSTGRE_HOST'];
        $user = $user ?: $_ENV['APP_POSTGRE_USER'];
        $pass = $pass ?: $_ENV['APP_POSTGRE_PASS'];


        $conn = new Postgre("pgsql:host={$host};dbname={$db};", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);


        $conn->exec("SET NAMES 'UTF8'");


        if ($schema)
        {
            $conn->exec("SET search_path TO {$schema}");
        }

        return $conn;
    }
}