<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:51
 */

namespace Holder\Cron\DividendYeld;


use GuzzleHttp\Client;
use Holder\Util\Cron\Handler;
use simplehtmldom_1_5\simple_html_dom;
use Sunra\PhpSimple\HtmlDomParser;

class DownloadDividends extends Handler
{
    protected function _getLabel(): string
    {
        return 'DOWNLOADING DIVIDENDS';
    }


    protected function _run(): void
    {
        $html = $this->getHtml();


        $trs = $html->find('#resultado tr');


        // removing table header
        unset($trs[0]);


        foreach ($trs as $i => $tr)
        {
            $date = $tr->children(0)->innertext();
            $value = $tr->children(1)->innertext();
            $forHowManyShares = (int)$tr->children(3)->innertext();


            list(, , $year) = array_map('intval', explode('/', $date));
            $value = (float)str_replace(',', '.', $value);


            if ($year < ($this->p->startYear - 1))
                break;


            $dividend = $value / $forHowManyShares;


            if (!isset($this->p->dividends[$date]))
                $this->p->dividends[$date] = 0;


            $this->p->dividends[$date] += $dividend;
        }


        // sort from oldest to newest
        $this->p->dividends = array_reverse($this->p->dividends);
    }


    private function getHtml(): simple_html_dom
    {
        $config = [
            'base_uri' => 'http://www.fundamentus.com.br/proventos.php?tipo=2&papel=' . $this->p->stock
        ];


        $client = new Client($config);


        $response = $client->request('GET');
        $body = (string)$response->getBody();


        $dom = HtmlDomParser::str_get_html($body);

        return $dom;
    }
}