<?php

namespace Gcd\Cyclops\Commands;

use Gcd\Cyclops\UseCases\PushDeletedToCyclopsUseCase;

abstract class PushDeletedToCyclopsCommand extends PushStateToCyclopsCommand
{
    final function executeUseCase()
    {
        $pushUseCase = new PushDeletedToCyclopsUseCase($this->getService());
        $pushUseCase->execute($this->getList(), $this->onCustomerDeleted());
    }

    abstract protected function onCustomerDeleted(): callable;
}
