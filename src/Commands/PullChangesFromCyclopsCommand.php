<?php

namespace Gcd\Cyclops\Commands;

use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\UseCases\PullChangesFromCyclopsUseCase;
use Rhubarb\Custard\Command\CustardCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class PullChangesFromCyclopsCommand extends CustardCommand
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pullUseCase = new PullChangesFromCyclopsUseCase($this->getService());
        $latestDate = $pullUseCase->execute($this->getLastRunTime(), $this->getCallable());

        $this->setLastRunTime($latestDate);
    }

    abstract protected function getService(): CyclopsService;

    abstract protected function getLastRunTime(): \DateTime;

    abstract protected function getCallable(): callable;

    abstract protected function setLastRunTime(\DateTime $date);
}
