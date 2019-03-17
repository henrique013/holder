<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:21
 */

namespace Holder\Cron\DividendYeld;


use GuzzleHttp\Client;
use Holder\Util\Cron\Handler;

class DownloadQuotations extends Handler
{
    protected function _getLabel(): string
    {
        return 'DOWNLOADING QUOTATIONS';
    }


    protected function _run(): void
    {
        $json = $this->getJson();
        $quotations = [];


        while ($item = array_shift($json))
        {
            $time = $item[0];
            $quotation = $item[1];


            $time /= 1000;
            $month = (int)date('m', $time);
            $year = (int)date('Y', $time);


            if (!isset($quotations[$year]))
            {
                $quotations[$year]['months'] = [];
                $quotations[$year]['sum'] = 0;
                $quotations[$year]['count'] = 0;
            }


            $quotations[$year]['months'][$month] = true;
            $quotations[$year]['sum'] += $quotation;
            $quotations[$year]['count']++;
        }


        foreach ($quotations as $year => $quotationsYear)
        {
            if (count($quotationsYear['months']) !== 12) continue;


            $quotation = $quotationsYear['sum'] / $quotationsYear['count'];


            $this->p->quotations[$year] = $quotation;
        }
    }


    private function getJson(): array
    {
        $config = [
            'base_uri' => 'http://www.fundamentus.com.br/amline/cot_hist.php?papel=' . $this->p->stock
        ];


        $client = new Client($config);


        $response = $client->request('GET');
        $body = (string)$response->getBody();
        $json = \GuzzleHttp\json_decode($body, true);


        return $json;
    }
}