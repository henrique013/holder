<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:16
 */

namespace Holder\Util\Cron;


use Monolog\Logger;

abstract class Script
{
    /** @var \Monolog\Logger */
    protected $logger;


    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }
}