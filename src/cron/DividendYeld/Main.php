<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:13
 */

namespace Holder\Cron\DividendYeld;


use DateTime;
use Holder\Util\Cron\Boot;
use Holder\Util\Helper;
use League\Pipeline\PipelineBuilder;
use stdClass;

class Main extends Boot
{
    public function run(array $params): void
    {
        $stocks = explode(',', $params['stocks']);


        $conn = Helper::connectPostgre();
        $startDt = (new DateTime)->modify('-3 years');
        $startYear = (int)$startDt->format('Y');


        foreach ($stocks as $stock)
        {
            $this->logger->info("stock: {$stock}");


            $payload = new stdClass();
            $payload->startYear = $startYear;
            $payload->stock = $stock;
            $payload->quotations = [];
            $payload->dividends = [];
            $payload->dividendYeld = [];


            $pipelineBuilder = (new PipelineBuilder)
                ->add(new DownloadQuotations($this->logger))
                ->add(new DownloadDividends($this->logger))
                ->add(new ConsolidateData($this->logger, $conn, $startDt));


            /** @var $pipeline \League\Pipeline\Pipeline */
            $pipeline = $pipelineBuilder->build();
            $pipeline->process($payload);
        }
    }
}