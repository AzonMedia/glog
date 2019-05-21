<?php

namespace Azonmedia\Glog\Tasks;

use Guzaba2\Base\Base;

class FinishHandler extends Base
{
    public function __invoke() : void
    {
        $this->handle();
    }

    public function handle() : void
    {

    }
}