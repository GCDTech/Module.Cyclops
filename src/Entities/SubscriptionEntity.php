<?php

namespace Gcd\Cyclops\Entities;

class SubscriptionEntity
{
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var bool
     */
    public $subscribed;

    public function __construct(string $id, string $name, bool $subscribed = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->subscribed = $subscribed;
    }
}