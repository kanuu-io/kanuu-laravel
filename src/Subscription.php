<?php

namespace Kanuu\Laravel;

use Illuminate\Support\Arr;

class Subscription
{
    /** @var bool */
    protected $isTrialing;

    /** @var bool */
    protected $isSubscribed;

    /** @var string */
    protected $status;

    /** @var int */
    protected $planId;

    /** @var int */
    protected $subscriptionId;

    public function __construct(bool $isTrialing, bool $isSubscribed, string $status, int $planId, int $subscriptionId)
    {
        $this->isTrialing = $isTrialing;
        $this->isSubscribed = $isSubscribed;
        $this->status = $status;
        $this->planId = $planId;
        $this->subscriptionId = $subscriptionId;
    }

    /**
     * @param array $subscription
     * @return static
     */
    public static function fromKanuu(array $subscription): self
    {
        return new static(
            Arr::get($subscription, 'is_trialing'),
            Arr::get($subscription, 'is_subscribed'),
            Arr::get($subscription, 'status'),
            Arr::get($subscription, 'plan_id'),
            Arr::get($subscription, 'subscription_id'),
        );
    }

    public function isTrialing(): bool
    {
        return $this->isTrialing;
    }

    public function isSubscribed(): bool
    {
        return $this->isSubscribed;
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isPausedOrCancelled(): bool
    {
        return $this->isPaused() || $this->isCancelled();
    }

    public function onGracePeriod(): bool
    {
        return $this->isPausedOrCancelled() && $this->isSubscribed();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getSubscriptionId(): int
    {
        return $this->subscriptionId;
    }
}
