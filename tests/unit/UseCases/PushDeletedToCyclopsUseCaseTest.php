<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsCustomerListEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\PushDeletedToCyclopsUseCase;

class PushDeletedToCyclopsUseCaseTest extends CyclopsTestCase
{
    public function testDeletedPushedToCyclops()
    {
        $deletedCount = $count = 0;
        $service = Stub::make(CyclopsService::class, [
            'loadCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                return $customer;
            },
            'getBrandOptInStatus' => function (CustomerEntity $customerEntity): bool {
                return false;
            },
            'deleteCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$deletedCount): bool {
                $deletedCount++;
                return true;
            },
        ]);

        verify($deletedCount)->equals(0);

        $createEntity = function ($email) use ($service) {
            $id = new CyclopsIdentityEntity();
            $id->email = $email;
            return $service->loadCustomer($id);
        };

        $list = new CyclopsCustomerListEntity();
        $list->items = [$createEntity('test@test.com'), $createEntity('test@testtest.com')];

        $useCase = new PushDeletedToCyclopsUseCase($service);
        $useCase->execute($list, function() {});
        verify($deletedCount)->equals(2);
    }
}
