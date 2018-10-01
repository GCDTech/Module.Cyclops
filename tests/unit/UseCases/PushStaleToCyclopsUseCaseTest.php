<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\PushStaleToCyclopsUseCase;

class PushStaleToCyclopsUseCaseTest extends CyclopsTestCase
{
    public function testStalePushedToCyclops()
    {
        $count = $staleCount = 0;
        $service = Stub::make(CyclopsService::class, [
            'createCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                $customer->brandOptIn = true;
                return $customer;
            },
            'setBrandOptInStatus' => function (CustomerEntity $customerEntity, bool $optIn) use (&$staleCount) {
                $staleCount++;
            },
        ]);

        verify($staleCount)->equals(0);

        $createEntity = function($email) use ($service) {
            $id = new CyclopsIdentityEntity();
            $id->email = $email;
            return $service->createCustomer($id);
        };

        $list = new CyclopsCustomerListEntity();
        $list->items = [$createEntity('test@test.com'), $createEntity('test@testtest.com')];

        $useCase = new PushStaleToCyclopsUseCase($service);
        $useCase->execute($list);
        verify($staleCount)->equals(2);
    }
}
