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


        while ($item = array_shift($json))
        {
            $time = $item[0];
            $quotation = $item[1];


            $time /= 1000;
            $date = date('d/m/Y', $time);
            $year = (int)date('Y', $time);


            if ($year < 2005)
                break;


            $this->p->quotations[$date] = $quotation;
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


        $json = array_reverse($json);

        return $json;
    }
}