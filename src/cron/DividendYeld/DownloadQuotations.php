<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:21
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Handler;

class DownloadQuotations extends Handler
{
    public function _getLabel(): string
    {
        return 'DOWNLOADING QUOTATIONS';
    }


    public function _getContent(): array
    {
        $ret = [];

        return $ret;
    }

    public function _process(array $content): void
    {

    }
}