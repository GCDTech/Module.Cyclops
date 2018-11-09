<?php

namespace Gcd\Cyclops\Settings;

class CyclopsSettings
{
    public $cyclopsUrl = '';
    public $authorizationUsername = '';
    public $authorizationPassword = '';
    public $enableLogging = false;
    public $enableApi = false;

    static $singleton;

    public static function singleton()
    {
        if (!self::$singleton) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }
}
