<?php

namespace Kanuu\Laravel\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kanuu\Laravel\Models\Subscription;

trait HasSubscriptions
{
    /**
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return $this->subscriptions()->active()->exists();
    }

    /**
     * @return null|Subscription
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()->latest()->active()->first();
    }

    /**
     * @return bool
     */
    public function onGracePeriod(): bool
    {
        return ($subscription = $this->activeSubscription())
            ? $subscription->onGracePeriod()
            : false;
    }
}
