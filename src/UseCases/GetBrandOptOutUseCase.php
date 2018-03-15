<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;

class GetBrandOptOutUseCase
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
        if (!$identityEntity->id) {
            $customer = $this->cyclopsService->createCustomer($identityEntity);
        } else {
            $customer = $this->cyclopsService->loadCustomer($identityEntity);
        }

        $optOut = $this->cyclopsService->getBrandOptOutStatus($customer);
        $customer->brandOptOut = $optOut;
        return $customer;
    }
}