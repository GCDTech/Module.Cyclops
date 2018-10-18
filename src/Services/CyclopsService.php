<?php

namespace Gcd\Cyclops\Services;

use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Exceptions\ConflictException;
use Gcd\Cyclops\Exceptions\CustomerNotFoundException;
use Gcd\Cyclops\Exceptions\CyclopsException;
use Gcd\Cyclops\Exceptions\UserForbiddenException;
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
            $response = $this->doCyclopsRequest($request);
        } else {
            $url = $this->cyclopsUrl . "customer?email={$identityEntity->email}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);

            $response = $this->doCyclopsRequest($request);
            $responseBody = json_decode($response->getResponseBody());
            if ($responseBody) {
                $identityEntity->id = $responseBody->data[0]->cyclopsId;
            }
        }

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            case 404:
                throw new CustomerNotFoundException();
                break;
            default:
                throw new CyclopsException();
        }

        $customer = new CustomerEntity();
        $customer->identity = $identityEntity;
        return $customer;
    }

    protected function doCyclopsRequest(HttpRequest $request)
    {
        return $this->httpClient->getResponse($request);
    }

    public function loadCustomer(CyclopsIdentityEntity $identityEntity): CustomerEntity
    {
        if ($identityEntity->id != '') {
            $url = $this->cyclopsUrl . "customer?cyclopsId={$identityEntity->id}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);

            $response = $this->doCyclopsRequest($request);
        } else {
            $url = $this->cyclopsUrl . "customer?email={$identityEntity->email}";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);

            $response = $this->doCyclopsRequest($request);
            $responseBody = json_decode($response->getResponseBody());
            $identityEntity->id = $responseBody->data[0]->cyclopsId;
        }

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            case 404:
                throw new CustomerNotFoundException();
                break;
            default:
                throw new CyclopsException();
        }

        $customer = new CustomerEntity();
        $customer->identity = $identityEntity;
        return $customer;
    }

    public function deleteCustomer(CyclopsIdentityEntity $identityEntity)
    {
        $url = $this->cyclopsUrl . "customer/{$identityEntity->id}";
        $request = new HttpRequest($url, 'delete');
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->doCyclopsRequest($request);

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            case 404:
                throw new CustomerNotFoundException();
                break;
            default:
                throw new CyclopsException();
        }

        return $response;
    }

    public function getBrandOptInStatus(CustomerEntity $customerEntity): bool
    {
        $optIn = false;
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/brands";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->doCyclopsRequest($request);

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            case 404:
                throw new CustomerNotFoundException();
                break;
            default:
                throw new CyclopsException();
        }

        if ($responseBody = json_decode($response->getResponseBody())) {
            foreach ($responseBody->data as $data) {
                if ($data->brandId == $this->brandId) {
                    $optIn = $data->optIn;
                }
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
        $request->addHeader('Content-Type', 'application/json');
        $response = $this->doCyclopsRequest($request);

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            case 404:
                throw new CustomerNotFoundException();
                break;
            case 409:
                throw new ConflictException();
            default:
                throw new CyclopsException();
        }

        return $response;
    }

    public function getBrandOptInStatusChanges(\DateTime $startingDate)
    {
        $url = $this->cyclopsUrl . "customer/optins?starting_at={$startingDate->format('Y-m-d\TH:i:s\Z')}";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->doCyclopsRequest($request);

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                throw new UserForbiddenException();
                break;
            default:
                throw new CyclopsException();
        }

        $changes = [];
        if ($responseBody = json_decode($response->getResponseBody())) {
            foreach ($responseBody->data as $data) {
                if ($data->cyclopsId) {
                    $changes[] = [$data->cyclopsId => $data->optIn];
                }
            }
        }

        return $changes;
    }
}
