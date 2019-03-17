<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:54
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Handler;
use Holder\Util\DB\Postgre;
use Monolog\Logger;

class ConsolidateData extends Handler
{
    /** @var Postgre */
    protected $conn;


    public function __construct(Logger $logger, Postgre $conn)
    {
        parent::__construct($logger);


        $this->conn = $conn;
    }

    public function _getLabel(): string
    {
        return 'CONSOLIDATING DATA';
    }


    public function _getContent(): array
    {
        $ret = [];

        return $ret;
    }

    public function _process(array $content): void
    {

    }
}