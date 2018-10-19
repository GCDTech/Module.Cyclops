<?php

namespace Gcd\Cyclops\Commands;

use Gcd\Cyclops\UseCases\PushStaleToCyclopsUseCase;

abstract class PushStaleToCyclopsCommand extends PushStateToCyclopsCommand
{
    final function executeUseCase()
    {
        $pushUseCase = new PushStaleToCyclopsUseCase($this->getService());
        $pushUseCase->execute($this->getList(), $this->onCustomerCreated());
    }

    abstract protected function onCustomerCreated(): callable;
}
