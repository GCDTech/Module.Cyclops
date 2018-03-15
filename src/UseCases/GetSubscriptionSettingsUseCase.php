<?php

namespace Gcd\Cyclops\UseCases;

use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Entities\SubscriptionEntity;
use Gcd\Cyclops\Services\CyclopsService;

/**
 * Class GetSubscriptionSettingsUseCase
 * @package Gcd\Cyclops\UseCases
 * @deprecated
 */
class GetSubscriptionSettingsUseCase
{
    /**
     * @var CyclopsService
     */
    private $cyclopsService;

    public function __construct(CyclopsService $cyclopsService)
    {
        $this->cyclopsService = $cyclopsService;
    }

    public function execute(CyclopsIdentityEntity $identityEntity)
    {
        if (!$identityEntity->id) {
            $customer = $this->cyclopsService->createCustomer($identityEntity);
        } else {
            $customer = $this->cyclopsService->loadCustomer($identityEntity);
        }

        $subscriptions = $this->cyclopsService->getListOfSubscriptions();
        $newSubscriptions = [];

        if (count($subscriptions) == 0) {
            $newSubscriptions = $customer->subscriptions;
        }

        foreach ($subscriptions as $id => $name) {
            $subscriptionEntity = new SubscriptionEntity($id, $name, false);

            foreach ($customer->subscriptions as $customerSubscription) {
                if ($customerSubscription->id == $id) {
                    $subscriptionEntity = $customerSubscription;
                }
            }

            $newSubscriptions[] = $subscriptionEntity;
        }

        $customer->subscriptions = $newSubscriptions;

        return $customer;
    }
}