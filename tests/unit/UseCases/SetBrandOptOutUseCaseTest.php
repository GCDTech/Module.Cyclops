<?php

namespace Gcd\Cyclops\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;

class SetBrandOptOutUseCaseTest extends CyclopsTestCase
{
    public function testCyclopsIdGetsCreated()
    {
        $count = 0;
        $service = Stub::make(CyclopsService::class, [
            'createCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                return $customer;
            },
            'loadCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                return $customer;
            },
            'setBrandOptOutStatus' => function (CustomerEntity $customerEntity, bool $optOut) {

            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = "joe@hotmail.com";

        $useCase = new SetBrandOptOutUseCase($service);
        $response = $useCase->execute($id, false);

        verify($response)->notNull();
        verify($response->identity->id)->notNull();

        $id = new CyclopsIdentityEntity();
        $id->email = "test@hotmail.com";

        $useCase = new SetBrandOptOutUseCase($service);
        $response2 = $useCase->execute($id, false);

        verify($response2->identity->id)->notSame($response->identity->id);

        $id = new CyclopsIdentityEntity();
        $id->email = "test@hotmail.com";
        $id->id = 24;

        $useCase = new SetBrandOptOutUseCase($service);
        $response3 = $useCase->execute($id, false);

        verify($response3->identity->id)->equals(24);
    }
}