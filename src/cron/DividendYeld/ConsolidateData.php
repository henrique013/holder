<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:54
 */

namespace Holder\Cron\DividendYeld;


use DateTime;
use Holder\Util\Cron\Handler;
use Holder\Util\DB\Postgre;
use Monolog\Logger;

class ConsolidateData extends Handler
{
    /** @var Postgre */
    protected $conn;
    /** @var DateTime */
    private $startDt;


    public function __construct(Logger $logger, Postgre $conn, DateTime $startDt)
    {
        parent::__construct($logger);


        $this->conn = $conn;
        $this->startDt = $startDt;
    }


    protected function _getLabel(): string
    {
        return 'CONSOLIDATING DATA';
    }


    protected function _run(): void
    {
        $currDt = clone $this->startDt;
        $endDt = new DateTime;


        for (; $currDt <= $endDt; $currDt->modify('+1 day'))
        {
            $weekDay = (int)$currDt->format('N');


            // skip the weekends
            if ($weekDay > 5)
                continue;


            $yeld = $this->calcDividendYeld($currDt);
            $date = $currDt->format('d/m/Y');


            $this->p->dividendYeld[$date] = $yeld;
        }


        $this->p->dividendYeld = array_filter($this->p->dividendYeld);
    }


    private function calcDividendYeld(DateTime $dt): float
    {
        $currDt = (clone $dt)->modify('-1 year');
        $dividends = 0;


        while ($currDt <= $dt)
        {
            $dateDDMMYYYY = $currDt->format('d/m/Y');
            $value = $this->p->dividends[$dateDDMMYYYY] ?? 0;


            $dividends += $value;


            $currDt->modify('+1 day');
        }


        $date = $dt->format('d/m/Y');
        $quotation = $this->p->quotations[$date] ?? 0;


        $ret = ($quotation) ? (($dividends / $quotation) * 100) : 0;


        return $ret;
    }
}