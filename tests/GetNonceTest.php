<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Kanuu\Laravel\Facades\Kanuu;

/**
 * @see \Kanuu\Laravel\Kanuu::getNonce
 */
class GetNonceTest extends TestCase
{
    /** @test */
    public function it_returns_a_new_nonce_and_the_url_to_redirect_to()
    {
        // Given the following mocked response.
        $this->mockKanuuHttpCall($mockedResponse = [
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ]);

        // When we fetch a new nonce for an identifier.
        $response = Kanuu::getNonce('some_identifier');

        // Then we returned the mocked API response.
        $this->assertEquals($mockedResponse, $response);

        // And we sent the right request to Kanuu.
        $this->assertKanuuHttpCallWasSent();
    }
}
