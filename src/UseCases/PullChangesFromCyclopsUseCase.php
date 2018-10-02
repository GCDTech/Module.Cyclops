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

    public function execute(\DateTime $changesSince, callable $setOptIn): \DateTime
    {
        try {
            $statusChanges = $this->cyclopsService->getBrandOptInStatusChanges($changesSince);

            foreach ($statusChanges as $cyclopsId => $optIn) {
                $setOptIn($cyclopsId, $optIn);
            }

            return new \DateTime();
        } catch (CyclopsException $exception) {
            return $changesSince;
        }
    }
}
