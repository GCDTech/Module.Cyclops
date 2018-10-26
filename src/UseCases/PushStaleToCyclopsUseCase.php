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

    public function execute(CyclopsCustomerListEntity $list, callable $onCustomerCreated)
    {
        foreach ($list->items as $item) {
            try {
                $this->cyclopsService->setBrandOptInStatus($item);

                $onCustomerCreated($item);
            } catch (CustomerNotFoundException $exception) {
                $customer = $this->cyclopsService->loadCustomer($item->identity);
                $customer->brandOptIn = $item->brandOptIn;
                $this->cyclopsService->setBrandOptInStatus($customer);

                $onCustomerCreated($customer);
            }
        }
    }
}
