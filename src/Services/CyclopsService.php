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
use Gcd\Cyclops\Http\HttpResponse;
use Gcd\Cyclops\Settings\CyclopsSettings;

class CyclopsService
{
    private $brandId;

    private $httpClient;

    private $cyclopsUrl;

    private $authorization;

    private $enableLogging;

    private $enableApi;

    const TIMESTAMP_FORMAT = 'Y-m-d\TH:i:s\Z';

    public function __construct(int $brandId)
    {
        $this->brandId = $brandId;
        $this->httpClient = new CurlHttpClient();
        $settings = CyclopsSettings::singleton();
        $this->cyclopsUrl = $settings->cyclopsUrl;
        $this->authorization = base64_encode($settings->authorizationUsername . ':' . $settings->authorizationPassword);
        $this->enableLogging = $settings->enableLogging;
        $this->enableApi = $settings->enableApi;
    }

    private function logCyclopsErrors(HttpRequest $request, HttpResponse $response)
    {
        $loggableRequest = clone $request;
        if (isset($loggableRequest->getHeaders()['Authorization'])) {
            $loggableRequest->addHeader('Authorization', '[[REDACTED]]');
        }
        if ($this->enableLogging) {
            error_log('Cyclops exception response: '
                . $response->getResponseCode()
                . ' '
                . $response->getResponseBody()
                .
                "\n Request: "
                . var_export($loggableRequest, true));
        }
    }

    public function loadCustomer(CyclopsIdentityEntity $identityEntity, \DateTime $timestamp = null): CustomerEntity
    {
        if (is_null($timestamp)) {
            $timestamp = new \DateTime('now');
        }

        $timestamp = $timestamp->format(self::TIMESTAMP_FORMAT);

        if ($identityEntity->id != '') {
            $url = $this->cyclopsUrl . "customer?cyclopsId={$identityEntity->id}&timestamp=$timestamp";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);
            $response = $this->doCyclopsRequest($request);
        } else {
            $email = urlencode($identityEntity->email);
            $url = $this->cyclopsUrl . "customer?email=$email&timestamp=$timestamp";
            $request = new HttpRequest($url);
            $request->addHeader('Authorization', 'Basic ' . $this->authorization);

            $response = $this->doCyclopsRequest($request);
        }

        $responseBody = json_decode($response->getResponseBody());
        if ($responseBody) {
            $identityEntity->id = $responseBody->data[0]->cyclopsId;
        }

        $customer = new CustomerEntity();
        $customer->identity = $identityEntity;
        return $customer;
    }

    protected function doCyclopsRequest(HttpRequest $request)
    {
        if (!$this->enableApi) {
            throw new CyclopsException('Cyclops API disabled');
        }

        $response = $this->httpClient->getResponse($request);

        switch ($response->getResponseCode()) {
            case 200:
                break;
            case 403:
                $this->logCyclopsErrors($request, $response);
                throw new UserForbiddenException();
                break;
            case 404:
                $this->logCyclopsErrors($request, $response);
                throw new CustomerNotFoundException();
                break;
            case 409:
                $this->logCyclopsErrors($request, $response);
                throw new ConflictException();
            default:
                $this->logCyclopsErrors($request, $response);
                throw new CyclopsException();
        }

        return $response;
    }

    public function deleteCustomer(CyclopsIdentityEntity $identityEntity, \DateTime $timestamp = null)
    {
        if (is_null($timestamp)) {
            $timestamp = new \DateTime('now');
        }

        $timestamp = $timestamp->format(self::TIMESTAMP_FORMAT);

        $url = $this->cyclopsUrl . "customer/{$identityEntity->id}?timestamp=$timestamp";
        $request = new HttpRequest($url, 'delete');
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        return $this->doCyclopsRequest($request);
    }

    public function getBrandOptInStatus(CustomerEntity $customerEntity): bool
    {
        $optIn = false;
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/brands";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->doCyclopsRequest($request);

        if ($responseBody = json_decode($response->getResponseBody())) {
            foreach ($responseBody->data as $data) {
                if ($data->brandId == $this->brandId) {
                    $optIn = $data->optIn;
                }
            }
        }

        return $optIn;
    }

    public function setBrandOptInStatus(CustomerEntity $customerEntity)
    {
        if (!$customerEntity->timestamp  || !$customerEntity->timestamp instanceof \DateTime) {
            $customerEntity->timestamp = new \DateTime('now');
        }

        $timestamp = $customerEntity->timestamp->format(self::TIMESTAMP_FORMAT);

        $brands = json_encode([
            [
                'brandId' => $this->brandId,
                'optIn' => $customerEntity->brandOptIn,
                'timestamp' => $timestamp
            ]
        ]);
        $url = $this->cyclopsUrl . "customer/{$customerEntity->identity->id}/brands";

        $request = new HttpRequest($url, 'post', $brands);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $request->addHeader('Content-Type', 'application/json');
        return $this->doCyclopsRequest($request);
    }

    public function getBrandOptInStatusChanges(\DateTime $startingDate, int $page = 1)
    {
        $url = $this->cyclopsUrl . "customer/optins?starting_at={$startingDate->format(self::TIMESTAMP_FORMAT)}&page={$page}";
        $request = new HttpRequest($url);
        $request->addHeader('Authorization', 'Basic ' . $this->authorization);
        $response = $this->doCyclopsRequest($request);

        $changes = [];
        if ($responseBody = json_decode($response->getResponseBody())) {
            foreach ($responseBody->data as $data) {
                if ($data->cyclopsId) {
                    $changes[] = [$data->cyclopsId => $data];
                }
            }
        }

        return $changes;
    }
}
