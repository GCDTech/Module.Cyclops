<?php

namespace Gcd\Cyclops\Commands;

use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Rhubarb\Custard\Command\CustardCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class PushStateToCyclopsCommand extends CustardCommand
{
    final function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executeUseCase();
        return 0;
    }

    abstract protected function getService(): CyclopsService;

    abstract protected function getList(): CyclopsCustomerListEntity;

    abstract protected function executeUseCase();
}
