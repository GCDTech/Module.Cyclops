<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Exceptions\CustomerNotFoundException;
use Gcd\Cyclops\Services\CyclopsService;

class PushStaleToCyclopsUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(CyclopsCustomerListEntity $list)
    {
        foreach ($list->items as $item) {
            try {
                $this->cyclopsService->setBrandOptInStatus($item, $item->brandOptIn);
            } catch (CustomerNotFoundException $exception) {
                $customer = $this->cyclopsService->loadCustomer($item->identity);
                $this->cyclopsService->setBrandOptInStatus($customer, $item->brandOptIn);
            }
        }
    }
}
