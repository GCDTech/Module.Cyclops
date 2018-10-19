<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\PullChangesFromCyclopsUseCase;

class PullChangesFromCyclopsUseCaseTest extends CyclopsTestCase
{
    public function testChangesPulledFromCyclops()
    {
        $cyclopsId = 12;

        $service = Stub::make(CyclopsService::class, [
            'loadCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                return $customer;
            },
            'getBrandOptInStatusChanges' => function (\DateTime $startingDate) use ($cyclopsId) {
                return [
                    'data' => [
                        'optIn' => false
                    ],
                ];
            }
        ]);

        $count = 0;
        $useCase = new PullChangesFromCyclopsUseCase($service);
        $useCase->execute(new \DateTime('2018-08-01 00:00:00'), function() use (&$count) {
            $count++;
        });

        verify($count)->equals(1);
    }
}
