<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 17/03/2019
 * Time: 13:44
 */

namespace Holder\Util\Datetime;


use DateTime;

class Date
{
    private function __construct() { }


    public static function getDatetimeFromDtBR(string $dt): DateTime
    {
        $dt = implode('-', array_reverse(explode('/', $dt)));
        $dt = new DateTime($dt);

        return $dt;
    }
}