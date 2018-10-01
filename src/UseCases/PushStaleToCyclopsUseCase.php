<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Exceptions\CyclopsException;
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
        foreach ($list as $item) {
            try {
                $this->cyclopsService->setBrandOptInStatus($item, $item->brandOptIn);
            } catch (CyclopsException $exception) {
            }
        }
    }
}
