<?php

namespace Gcd\Cyclops\Tests\unit;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Exceptions\CustomerNotFoundException;
use Gcd\Cyclops\Exceptions\CyclopsException;
use Gcd\Cyclops\Exceptions\UserForbiddenException;
use Gcd\Cyclops\Http\HttpRequest;
use Gcd\Cyclops\Http\HttpResponse;
use Gcd\Cyclops\Services\CyclopsService;

class CyclopsServiceTest extends CyclopsTestCase
{
    private $authorization = false;
    private $badRequest = false;

    private function stubService(string $urlNeedle)
    {
        return Stub::make(CyclopsService::class, [
            'doCyclopsRequest' => function (HttpRequest $request) use ($urlNeedle): HttpResponse {
                $response = new HttpResponse();
                $response->setResponseCode(200);

                if ($this->authorization == false) {
                    $response->setResponseCode(403);
                } elseif (strpos($request->getUrl(), $urlNeedle) !== false) {
                    $response->setResponseCode(404);
                } elseif ($this->badRequest) {
                    $response->setResponseCode(400);
                }

                return $response;
            },
        ]);
    }

    public function testLoadCustomerErrorResponses()
    {
        $service = $this->stubService('afr1tr');

        $identity = new CyclopsIdentityEntity();
        $identity->id = 'afr1tr';

        $assertException = function ($exceptionClass, $message) use ($service, $identity) {
            self::assertThrowsException(
                $exceptionClass,
                function () use ($identity, $service) {
                    $service->loadCustomer($identity);
                },
                $message
            );
        };

        $assertException(UserForbiddenException::class,
            "Should get an exception for trying to load a customer with a User who does not have read access");

        $this->authorization = true;
        $assertException(CustomerNotFoundException::class,
            "Should get an exception for trying to load a customer from a CyclopsID that doesn't exist");

        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }

    public function testCreateCustomerErrorResponses()
    {
        $service = Stub::make(CyclopsService::class, [
            'doCyclopsRequest' => function (HttpRequest $request): HttpResponse {
                $response = new HttpResponse();
                $response->setResponseCode(200);

                if ($this->authorization == false) {
                    $response->setResponseCode(403);
                } elseif ($this->badRequest) {
                    $response->setResponseCode(400);
                } elseif (strpos($request->getUrl(), 'test@test.com') !== false) {
                    $response->setResponseCode(404);
                }

                return $response;
            },
        ]);

        $identity = new CyclopsIdentityEntity();

        $assertException = function ($exceptionClass, $message) use ($service, $identity) {
            self::assertThrowsException(
                $exceptionClass,
                function () use ($identity, $service) {
                    $service->createCustomer($identity);
                },
                $message
            );
        };

        $assertException(UserForbiddenException::class,
            "Should get an exception for trying to load a customer with a User who does not have read access");

        $this->authorization = true;
        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");

        $this->badRequest = false;
        $identity->email = 'test@test.com';
        $assertException(CustomerNotFoundException::class, "Should get an exception for trying to load a customer from an email address that doesn't exist");
    }

    public function testDeleteCustomerErrorResponses()
    {
        $service = Stub::make(CyclopsService::class, [
            'doCyclopsRequest' => function (HttpRequest $request): HttpResponse {
                $response = new HttpResponse();
                $response->setResponseCode(200);

                if ($this->authorization == false) {
                    $response->setResponseCode(403);
                } elseif ($this->badRequest) {
                    $response->setResponseCode(400);
                } elseif (strpos($request->getUrl(), 'afr1tr') !== false) {
                    $response->setResponseCode(404);
                }

                return $response;
            },
        ]);

        $identity = new CyclopsIdentityEntity();
        $identity->id = 'afr1tr';

        $assertException = function ($exceptionClass, $message) use ($service, $identity) {
            self::assertThrowsException(
                $exceptionClass,
                function () use ($identity, $service) {
                    $service->deleteCustomer($identity);
                },
                $message
            );
        };


        $assertException(UserForbiddenException::class,
            "Should get an exception for trying to delete a customer with a User who does not have write access");

        $this->authorization = true;
        $assertException(CustomerNotFoundException::class,
            "Should get an exception for trying to delete a customer from a CyclopsID that doesn't exist");

        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }
}
