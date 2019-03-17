<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 20:51
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Handler;

class DownloadDividends extends Handler
{
    protected function _getLabel(): string
    {
        return 'DOWNLOADING DIVIDENDS';
    }


    protected function _run(): void
    {

    }
}