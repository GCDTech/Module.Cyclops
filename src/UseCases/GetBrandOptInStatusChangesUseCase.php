<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Services\CyclopsService;

class GetBrandOptInStatusChangesUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(string $startingDate)
    {
        return $this->cyclopsService->getBrandOptInStatusChanges($startingDate);
    }
}
