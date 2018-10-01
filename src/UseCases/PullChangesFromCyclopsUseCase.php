<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Exceptions\CyclopsException;
use Gcd\Cyclops\Services\CyclopsService;

class PullChangesFromCyclopsUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(string $changesSince, callable $setOptIn)
    {
        try {
            $statusChanges = $this->cyclopsService->getBrandOptInStatusChanges($changesSince);

            foreach ($statusChanges as $cyclopsId => $optIn) {
                $setOptIn($cyclopsId, $optIn);
            }

            if (file_exists('/cyclops/last-status-changes-date.txt')) {
                mkdir('/cyclops');
            }
            file_put_contents('/cyclops/last-status-changes-date.txt', date('Y-m-d H:i:s'));
        } catch (CyclopsException $exception) {
        }
    }
}
