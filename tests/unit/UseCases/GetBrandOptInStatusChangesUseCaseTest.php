<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\GetBrandOptInStatusChangesUseCase;

class GetBrandOptInStatusChangesUseCaseTest extends CyclopsTestCase
{
    private $optIn = true;

    public function testBrandOptInStatusChangesReturned()
    {
        $cyclopsId = 12;
        $service = Stub::make(CyclopsService::class, [
            'createCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                return $customer;
            },
            'getBrandOptInStatusChanges' => function (\DateTime $startingDate) use ($cyclopsId) {
                return [
                    'data' => [
                        'cyclopsId' => $cyclopsId,
                        'optIn' => $this->optIn,
                        'brandId' => '15',
                        'optIn_datetime' => '2018-09-25T15:15:00+0000',
                    ],
                ];
            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = 'test@hotmail.com';
        $id->id = $cyclopsId;
        $service->createCustomer($id);

        $useCase = new GetBrandOptInStatusChangesUseCase($service);
        $response = $useCase->execute(new \DateTime());

        verify($response['data']['cyclopsId'])->equals($cyclopsId);
        verify($response['data']['optIn'])->true();

        $this->optIn = false;
        $response = $useCase->execute(new \DateTime());
        verify($response['data']['optIn'])->false();
    }
}
