<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:13
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Boot;
use Holder\Util\Helper;
use League\Pipeline\PipelineBuilder;
use stdClass;

class Main extends Boot
{
    public function run(array $params): void
    {
        $conn = Helper::connectPostgre();


        $payload = new stdClass();
        $payload->quotations = [];
        $payload->dividends = [];


        $pipelineBuilder = (new PipelineBuilder)
            ->add(new DownloadQuotations($this->logger))
            ->add(new DownloadDividends($this->logger))
            ->add(new ConsolidateData($this->logger, $conn));


        /** @var $pipeline \League\Pipeline\Pipeline */
        $pipeline = $pipelineBuilder->build();
        $pipeline->process($payload);
    }
}