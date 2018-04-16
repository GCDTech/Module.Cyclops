<?php

namespace Gcd\Cyclops\Services;

use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Http\CurlHttpClient;
use Gcd\Cyclops\Http\HttpRequest;
use Gcd\Cyclops\Settings\CyclopsSettings;

class CyclopsService
{
    private $brandId;

    private $httpClient;

    private $cyclopsUrl;

    private $authorization;

    public function __construct(int $brandId)
    {
        $this->brandId = $brandId;
        $this->httpClient = new CurlHttpClient();
        $settings = CyclopsSettings::singleton();
        $this->cyclopsUrl = $settings->cyclopsUrl;
        $this->authorization = base64_encode($settings->authorizationUsername . ':' . $settings->authorizationPassword);
    }

    public function createCustomer(CyclopsIdentityEntity $identityEntity): CustomerEntity
    {
        if ($identityEntity->id != '') {
            $url = $this->cyclopsUrl . "customer?cyclopsId={$identityEntity->id}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);
            $this->httpClient->getResponse($request);
        } else {
            $url = $this->cyclopsUrl . "customer?email={$identityEntity->email}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);
            $response = json_decode($this->httpClient->getResponse($request)->getResponseBody());
            $identityEntity->id = $response->data[0]->cyclopsId;
        }

        $customer = new CustomerEntity();
        $customer->identity = $identityEntity;
        return $customer;
    }

    public function loadCustomer(CyclopsIdentityEntity $identityEntity): CustomerEntity
    {
        if ($identityEntity->id != '') {
            $url = $this->cyclopsUrl . "customer?cyclopsId={$identityEntity->id}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);
            $this->httpClient->getResponse($request);
        } else {
            $url = $this->cyclopsUrl . "customer?email={$identityEntity->email}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);
            $response = json_decode($this->httpClient->getResponse($request)->getResponseBody());
            $identityEntity->id = $response->data[0]->cyclopsId;
        }

        $customer = new CustomerEntity();
        $customer->identity = $identityEntity;
        return $customer;
    }

    public function getListOfSubscriptions(): array
    {
        $url = $this->cyclopsUrl . "newsletter/{$this->brandId}";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = json_decode($this->httpClient->getResponse($request)->getResponseBody());

        $subscriptions = [];

        foreach ($response->data as $data) {
            $subscriptions[$data->id] = $data->name;
        }

        return $subscriptions;
    }

    public function setSubscriptions(CustomerEntity $customerEntity, array $subscriptions)
    {
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/newsletters";
        $request = new HttpRequest($url, 'post', $subscriptions);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $this->httpClient->getResponse($request);
    }

    public function deleteCustomer(CyclopsIdentityEntity $identityEntity)
    {

    }

    public function getBrandOptInStatus(CustomerEntity $customerEntity): bool
    {
        $optIn = false;
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/brands";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = json_decode($this->httpClient->getResponse($request)->getResponseBody());
        foreach ($response->data as $data) {
            if ($data->brandId == $this->brandId) {
                $optIn = $data->optIn;
            }
        }

        return $optIn;
    }

    public function setBrandOptInStatus(CustomerEntity $customerEntity, bool $optIn)
    {
        $brands = json_encode([['brandId' => $this->brandId, 'optIn' => $optIn]]);
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/brands";

        $request = new HttpRequest($url, 'post', $brands);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->httpClient->getResponse($request);
        return $response;
    }
}