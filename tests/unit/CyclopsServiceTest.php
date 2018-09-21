<?php

namespace Gcd\Cyclops\Tests\unit;

use Codeception\Stub;
use Gcd\Cyclops\Entities\CustomerEntity;
use Gcd\Cyclops\Entities\CyclopsIdentityEntity;
use Gcd\Cyclops\Exceptions\ConflictException;
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
    private $conflict = false;

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
                } elseif ($this->conflict) {
                    $response->setResponseCode(409);
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

        $identity->id = 'test123';
        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }

    public function testCreateCustomerErrorResponses()
    {
        $service = $this->stubService('test@test.com');

        $identity = new CyclopsIdentityEntity();
        $identity->email = 'test@test.com';

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
        $assertException(CustomerNotFoundException::class,
            "Should get an exception for trying to load a customer from an email address that doesn't exist");
    }

    public function testDeleteCustomerErrorResponses()
    {
        $service = $this->stubService('afr1tr');

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

        $identity->id = 'test123';
        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }

    public function testGetBrandOptInErrorResponses()
    {
        $service = $this->stubService('afr1tr');

        $identity = new CyclopsIdentityEntity();
        $identity->id = 'afr1tr';
        $customer = new CustomerEntity();
        $customer->identity = $identity;

        $assertException = function ($exceptionClass, $message) use ($service, $customer) {
            self::assertThrowsException(
                $exceptionClass,
                function () use ($customer, $service) {
                    $service->getBrandOptInStatus($customer);
                },
                $message
            );
        };

        $assertException(UserForbiddenException::class,
            "Should get an exception for trying to get brand opt in status for a customer with a User who does not have read access");

        $this->authorization = true;
        $assertException(CustomerNotFoundException::class,
            "Should get an exception for trying to get brand opt in status for a customer from a CyclopsID that doesn't exist");

        $identity->id = 'test123';
        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }

    public function testSetBrandOptInErrorResponses()
    {
        $service = $this->stubService('afr1tr');

        $identity = new CyclopsIdentityEntity();
        $identity->id = 'afr1tr';
        $customer = new CustomerEntity();
        $customer->identity = $identity;

        $assertException = function ($exceptionClass, $message) use ($service, $customer) {
            self::assertThrowsException(
                $exceptionClass,
                function () use ($customer, $service) {
                    $service->setBrandOptInStatus($customer, false);
                },
                $message
            );
        };

        $assertException(UserForbiddenException::class,
            "Should get an exception for trying to set brand opt in status for a customer with a User who does not have write access");

        $this->authorization = true;
        $assertException(CustomerNotFoundException::class,
            "Should get an exception for trying to set brand opt in status for a customer from a CyclopsID that doesn't exist");

        $identity->id = 'test123';
        $this->conflict = true;
        $assertException(ConflictException::class,
            "Should get an exception for any conflicts when trying to set brand opt in status for a customer");

        $this->conflict = false;
        $this->badRequest = true;
        $assertException(CyclopsException::class, "Should get an exception for any other issues");
    }
}
