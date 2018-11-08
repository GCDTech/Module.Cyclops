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
                $this->cyclopsService->deleteCustomer($item->identity);
                if ($onCustomerDeleted !== null) {
                    $onCustomerDeleted($item, true);
                }
            } catch (CustomerNotFoundException $exception) {
                if ($onCustomerDeleted !== null) {
                    $onCustomerDeleted($item, false);
                }
            }
        }
    }
}
