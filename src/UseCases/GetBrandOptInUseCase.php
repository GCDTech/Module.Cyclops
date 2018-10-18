<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;

class GetBrandOptInUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(CyclopsIdentityEntity $identityEntity): CustomerEntity
    {
        $customer = $this->cyclopsService->loadCustomer($identityEntity);

        $optIn = $this->cyclopsService->getBrandOptInStatus($customer);
        $customer->brandOptIn = $optIn;
        return $customer;
    }
}
