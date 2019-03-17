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
    private const FIELD_DATE = 0;
    private const FIELD_QUOTATION = 1;


    protected function _getLabel(): string
    {
        return 'DOWNLOADING QUOTATIONS';
    }


    protected function _run(): void
    {
        $json = $this->getJson();


        while ($item = array_shift($json))
        {
            $date = $item[self::FIELD_DATE];
            $quotation = $item[self::FIELD_QUOTATION];


            $date /= 1000;
            $date = date('d/m/Y', $date);


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

        return $json;
    }
}