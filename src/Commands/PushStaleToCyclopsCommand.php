<?php

namespace Gcd\Cyclops\Commands;

use Gcd\Cyclops\UseCases\PushStaleToCyclopsUseCase;

abstract class PushStaleToCyclopsCommand extends PushStateToCyclopsCommand
{
    final function executeUseCase()
    {
        $pushUseCase = new PushStaleToCyclopsUseCase($this->getService());
        $pushUseCase->execute($this->getList(), $this->getCustomerPushedHandler());
    }

    abstract protected function getCustomerPushedHandler(): callable;
}
