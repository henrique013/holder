<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:13
 */

namespace Holder\Cron\DividendYeld;


use Holder\Util\Cron\Boot;

class Main extends Boot
{
    public function run(array $params): void
    {
        $this->logger->info(__FUNCTION__);
    }
}