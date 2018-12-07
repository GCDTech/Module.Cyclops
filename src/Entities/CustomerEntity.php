<?php

namespace Gcd\Cyclops\Entities;

class CustomerEntity
{
    /** @var CyclopsIdentityEntity */
    public $identity;

    /**
     * @var bool
     */
    public $brandOptIn;

    /**
     * @var \DateTime
     */
    public $timestamp;
}
