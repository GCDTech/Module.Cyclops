<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Exceptions\CustomerNotFoundException;
use Gcd\Cyclops\Exceptions\CyclopsException;
use Gcd\Cyclops\Services\CyclopsService;

class PushDeletedToCyclopsUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(CyclopsCustomerListEntity $list, callable $onCustomerDeleted = null)
    {
        foreach ($list->items as $item) {
            try {
                $this->cyclopsService->deleteCustomer($item->identity, $item->timestamp);

                if ($onCustomerDeleted !== null) {
                    // Set CyclopsQueue item to sent
                    $onCustomerDeleted($item, true);
                }
            } catch (CustomerNotFoundException $exception) {
                if ($onCustomerDeleted !== null) {
                    // Set CyclopsQueue item to not sent
                    $onCustomerDeleted($item, false);

                    // Throw new exception to trigger retry request and/or failure email
                    throw new CyclopsException(
                        "404 Customer Not Found and queue item not sent",
                        404,
                        $exception
                    );
                }
            }
        }
    }
}
