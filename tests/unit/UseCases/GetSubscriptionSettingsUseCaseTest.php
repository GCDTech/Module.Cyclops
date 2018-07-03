<?php

namespace Gcd\Cyclops\Tests\unit\UseCases;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Entities\SubscriptionEntity;
use Gcd\Cyclops\Services\CyclopsService;
use Gcd\Cyclops\Tests\unit\CyclopsTestCase;
use Gcd\Cyclops\UseCases\GetSubscriptionSettingsUseCase;

class GetSubscriptionSettingsUseCaseTest extends CyclopsTestCase
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
                $customer->subscriptions[] = new SubscriptionEntity(1, "test");
                return $customer;
            },
            'getListOfSubscriptions' => function (): array {
                return [];
            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = "joe@hotmail.com";

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response = $useCase->execute($id);

        verify($response)->notNull();
        verify($response->identity->id)->notNull();

        $id = new CyclopsIdentityEntity();
        $id->email = "test@hotmail.com";

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response2 = $useCase->execute($id);

        verify($response2->identity->id)->notSame($response->identity->id);

        $id = new CyclopsIdentityEntity();
        $id->email = "test@hotmail.com";
        $id->id = 24;

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response3 = $useCase->execute($id);

        verify($response3->identity->id)->equals(24);
    }

    public function testSubscriptionSettingsReturned()
    {
        $count = 0;
        $service = Stub::make(CyclopsService::class, [
            'createCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                $customer->subscriptions = [new SubscriptionEntity('id', 'name', true)];
                return $customer;
            },
            'getListOfSubscriptions' => function (): array {
                return [];
            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = "joe@hotmail.com";

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response = $useCase->execute($id);

        verify($response->subscriptions)->notNull();
        verify($response->subscriptions)->count(1);
        verify($response->subscriptions[0])->isInstanceOf(SubscriptionEntity::class);
        verify($response->subscriptions[0]->id)->notNull();
        verify($response->subscriptions[0]->name)->notNull();
        verify($response->subscriptions[0]->subscribed)->true();
    }

    public function testSubscriptionSettingsBlended()
    {
        $service = Stub::make(CyclopsService::class, [
            'createCustomer' => function (CyclopsIdentityEntity $identityEntity) use (&$count): CustomerEntity {
                $identityEntity->id = $count++;
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                $customer->subscriptions = [
                    new SubscriptionEntity('id1', 'name1', true),
                    new SubscriptionEntity('id2', 'name2', false),
                ];
                return $customer;
            },
            'getListOfSubscriptions' => function (): array {
                return [
                    'id1' => 'name1',
                    'id2' => 'name2',
                    'id3' => 'name3',
                ];
            },
        ]);

        $id = new CyclopsIdentityEntity();
        $id->email = "joe@hotmail.com";

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response = $useCase->execute($id);

        verify($response->subscriptions)->count(3);
        verify($response->subscriptions[0])->isInstanceOf(SubscriptionEntity::class);
        verify($response->subscriptions[0]->subscribed)->true();
        verify($response->subscriptions[1]->subscribed)->false();
    }

    public function testLoadingFromCyclops()
    {
        $id = new CyclopsIdentityEntity();
        $id->email = "joe@hotmail.com";
        $id->id = "123";

        $service = Stub::make(CyclopsService::class, [
            'loadCustomer' => function (CyclopsIdentityEntity $identityEntity): CustomerEntity {
                $customer = new CustomerEntity();
                $customer->identity = $identityEntity;
                $customer->subscriptions[] = new SubscriptionEntity(1, "test");
                return $customer;
            },
            'getListOfSubscriptions' => function (): array {
                return [
                    '1' => 'name1',
                    'id2' => 'name2',
                    'id3' => 'name3',
                ];
            },
        ]);

        $useCase = new GetSubscriptionSettingsUseCase($service);
        $response = $useCase->execute($id);

        verify($response->subscriptions[0]->id)->equals(1);
    }
}
