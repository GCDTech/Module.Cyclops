<?php

namespace Gcd\Cyclops\UseCases;

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
        $changesDate = '';
        $page = 1;
        do {
            $statusChanges = $this->cyclopsService->getBrandOptInStatusChanges($changesSince, $page);

            foreach ($statusChanges as $cyclopsId => $data) {
                $setOptIn($cyclopsId, $data['optIn']);

                if (isset($data->optinAt)) {
                    if ($changesDate == '' || $data->optinAt > $changesDate) {
                        $changesDate = $data->optinAt;
                    }
                }
            }

            $page++;
        } while ($statusChanges);


        return new \DateTime($changesDate);
    }
}
