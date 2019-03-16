<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:21
 */

namespace Holder\Cron;


use Holder\Util\Cron\Handler;

class DownloadQuotes extends Handler
{
    public function _getContent(): array
    {
        $this->logger->info(__FUNCTION__);


        $ret = [];

        return $ret;
    }

    public function _process(array $content): void
    {
        $this->logger->info(__FUNCTION__);
    }
}