<?php

namespace Kanuu\Laravel;

use Kanuu\Laravel\Facades\Kanuu as Kanuu;

trait Billable
{
    public function isSubscribed(): bool
    {
        return $this->getSubscription()->isSubscribed();
    }

    public function onTrial(): bool
    {
        return $this->getSubscription()->isTrialing();
    }

    public function onGracePeriod(): bool
    {
        return $this->getSubscription()->onGracePeriod();
    }

    public function getSubscription(): Subscription
    {
        $identifier = Kanuu::getIdentifier($this);

        return Kanuu::getCachedSubscription($identifier);
    }
}
