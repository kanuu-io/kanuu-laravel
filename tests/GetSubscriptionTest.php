<?php

namespace Kanuu\Laravel\Tests;

use Kanuu\Laravel\Facades\Kanuu;

/**
 * @see \Kanuu\Laravel\Kanuu::getSubscription
 */
class GetSubscriptionTest extends TestCase
{
    /** @test */
    public function it_returns_a_subscription_object_with_the_api_data()
    {
        // Given the following mocked response.
        $this->mockKanuuHttpCall([
            "is_trialing" => true,
            "is_subscribed" => true,
            "status" => "trialing",
            "plan_id" => 12345,
            "subscription_id" => 67890,
        ]);

        // When we fetch a subscription for a given identifier.
        $subscription = Kanuu::getSubscription('some_identifier');

        // Then it contains all the information from the mocked API Response.
        $this->assertTrue($subscription->isTrialing());
        $this->assertTrue($subscription->isSubscribed());
        $this->assertEquals('trialing', $subscription->getStatus());
        $this->assertEquals(12345, $subscription->getPlanId());
        $this->assertEquals(67890, $subscription->getSubscriptionId());

        // And we sent the right request to Kanuu.
        $this->assertHttpCallWasSent('POST', 'https://kanuu.io/api/subscription');
    }
}
