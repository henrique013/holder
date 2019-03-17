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


    protected function _getLabel(): string
    {
        return 'CONSOLIDATING DATA';
    }


    protected function _run(): void
    {
        foreach ($this->p->dividends as $year => $dividends)
        {
            $quotation = $this->p->quotations[$year] ?? 0;


            if (!$quotation) continue;


            $yeld = ($dividends / $quotation) * 100;


            $this->p->dividendYeld[$year] = $yeld;
        }
    }
}