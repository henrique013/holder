<?php
/**
 * Created by PhpStorm.
 * User: Henrique
 * Date: 16/03/2019
 * Time: 19:19
 */

namespace Holder\Util\Cron;


use stdClass;

abstract class Handler extends Script
{
    /** @var \stdClass */
    protected $p;


    public function __invoke(stdClass $p): stdClass
    {
        $this->p = $p;


        $this->logger->info('================================================================================');
        $this->logger->info($this->_getLabel());
        $this->logger->info('================================================================================');


        if ($content = $this->_getContent())
        {
            $this->_process($content);
        }

        return $this->p;
    }


    public abstract function _getLabel(): string;


    public abstract function _getContent(): array;


    public abstract function _process(array $content): void;
}