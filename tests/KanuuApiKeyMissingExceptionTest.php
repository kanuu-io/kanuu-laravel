<?php

namespace Kanuu\Laravel\Tests;

use Kanuu\Laravel\Exceptions\KanuuApiKeyMissingException;
use Kanuu\Laravel\Facades\Kanuu;

/**
 * @see KanuuApiKeyMissingException
 */
class KanuuApiKeyMissingExceptionTest extends TestCase
{
    /** @test */
    public function it_throws_an_exception_when_the_api_key_is_missing()
    {
        // Given no API Key is provided.
        $this->app['config']->set('kanuu.api_key', null);

        // Then we expect a useful exception.
        $this->expectException(KanuuApiKeyMissingException::class);
        $this->expectExceptionMessage('Your Kanuu API key is missing. Please ensure you added it to your .env file.');

        // When we try to access the nonce.
        Kanuu::getNonce('some_identifier');
    }
}
