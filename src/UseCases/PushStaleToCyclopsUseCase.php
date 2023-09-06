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

    public function execute(CyclopsCustomerListEntity $list, callable $onItemPushed)
    {
        foreach ($list->items as $item) {
            try {
                if (!$item->identity->id) {
                    throw new CustomerNotFoundException();
                }
                $this->cyclopsService->setBrandOptInStatus($item);

                // Set CyclopsQueue item to not sent
                $onItemPushed($item, false);
            } catch (CustomerNotFoundException $exception) {
                $customer = $this->cyclopsService->loadCustomer($item->identity, $item->timestamp);
                $customer->brandOptIn = $item->brandOptIn;
                $this->cyclopsService->setBrandOptInStatus($customer);

                // Set CyclopsQueue item to sent
                $onItemPushed($item, true, $customer);
            }
        }
    }
}
