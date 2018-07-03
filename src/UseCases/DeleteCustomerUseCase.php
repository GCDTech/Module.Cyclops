<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;

class DeleteCustomerUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(CyclopsIdentityEntity $identityEntity)
    {
        $this->cyclopsService->deleteCustomer($identityEntity);
    }
}