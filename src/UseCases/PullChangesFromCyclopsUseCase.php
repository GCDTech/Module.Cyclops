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
            $statusChanges = $this->cyclopsService->getBrandOptInStatusChanges($changesSince);

            foreach ($statusChanges as $cyclopsId => $data) {
                $setOptIn($cyclopsId, $data->optIn);

                if (!isset($changesDate) || $data->optinAt > $changesDate) {
                    $changesDate = $data->optinAt;
                }
            }

        return new \DateTime($changesDate);
    }
}
