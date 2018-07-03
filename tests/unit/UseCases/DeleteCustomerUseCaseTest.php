<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\DeleteCustomerUseCase;
use Gcd\Cyclops\UseCases\GetBrandOptInUseCase;

class DeleteCustomerUseCaseTest extends CyclopsTestCase
{
    public function testCustomerDeleted()
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
            'getBrandOptInStatus' => function (CustomerEntity $customerEntity): bool {
                return false;
            },
            'deleteCustomer' => function (CyclopsIdentityEntity $identityEntity): bool {
                return true;
            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = "test@test.com";

        $useCase = new GetBrandOptInUseCase($service);
        $response = $useCase->execute($id);
        verify($response->identity->id)->notNull();

        $deleteUseCase = new DeleteCustomerUseCase($service);
        $deleteUseCase->execute($response->identity);

        $response2 = $useCase->execute($id);
        verify($response2->identity->id)->notNull();
        verify($response2->identity->id)->notSame($response->identity->id);
    }
}