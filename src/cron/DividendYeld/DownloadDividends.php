<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:51
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Handler;
use Holder\Util\Datetime\Date;
use Sunra\PhpSimple\HtmlDomParser;

class DownloadDividends extends Handler
{
    protected function _getLabel(): string
    {
        return 'DOWNLOADING DIVIDENDS';
    }


    protected function _run(): void
    {
        $filename = substr($this->p->stock, 0, 4);
        $html = file_get_contents(__DIR__ . "/data/{$filename}.html");
        $html = HtmlDomParser::str_get_html($html);


        $trs = $html->find('tr');


        // removing table header
        unset($trs[0]);


        foreach ($trs as $i => $tr)
        {
            $stock = trim($tr->children(0)->innertext());
            $dateEx = trim($tr->children(2)->innertext());
            $date = trim($tr->children(3)->innertext());
            $value = trim($tr->children(4)->innertext());


            if ($stock !== $this->p->stock) continue;


            if (!preg_match('/\d\d\/\d\d\/\d\d\d\d/', $date))
            {
                $date = $dateEx;
            }


            $year = (int)Date::getDatetimeFromDtBR($date)->format('Y');
            $value = (float)str_replace(',', '.', $value);


            if (!isset($this->p->dividends[$year]))
            {
                $this->p->dividends[$year] = 0;
            }


            $this->p->dividends[$year] += $value;
        }


        // sort from oldest to newest
        $this->p->dividends = array_reverse($this->p->dividends, true);
    }
}