<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:13
 */

namespace Holder\Util\Cron;


abstract class Boot extends Script
{
    public abstract function run(array $params): void;
}