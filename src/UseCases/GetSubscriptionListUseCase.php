<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Services\CyclopsService;

class GetSubscriptionListUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute()
    {
        return $this->cyclopsService->getListOfSubscriptions();
    }
}