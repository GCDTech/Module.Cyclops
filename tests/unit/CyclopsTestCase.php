<?php

namespace Gcd\Cyclops\Tests\unit;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_Exception;

class CyclopsTestCase extends TestCase
{
    public static function assertThrowsException(
        $expectedException,
        callable $callable,
        $message = '',
        $expectedExceptionMessage = ''
    ) {
        $thrown = false;
        try {
            $callable();
        } catch (PHPUnit_Framework_Exception $exception) {
            throw $exception;
        } catch (\Exception $ex) {
            $thrown = true;
            self::assertInstanceOf($expectedException, $ex, $message);
            if (!empty($expectedExceptionMessage)) {
                self::assertEquals($expectedExceptionMessage, $ex->getMessage(), $message);
            }
        }

        self::assertTrue($thrown, $message !== null ? $message : "Expected a {$expectedException} to be thrown");
    }
}
