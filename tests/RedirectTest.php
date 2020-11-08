<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Kanuu\Laravel\Facades\Kanuu;

/**
 * @see \Kanuu\Laravel\Kanuu::redirect
 */
class RedirectTest extends TestCase
{
    /** @test */
    public function it_can_directly_redirect_to_the_kanuu_manage_page()
    {
        // Given the following mocked response.
        $mockedResponse = [
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ];

        // Used by the fake Http facade.
        Http::fake(['*' => Http::response($mockedResponse, 200)]);

        // When we redirect to Kanuu with some identifier.
        $response = Kanuu::redirect('some_identifier');

        // Then we returned a RedirectResponse towards the url provided in the mocked response.
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertTrue($response->isRedirect('https://kanuu.io/manage/some_team/some_nonce'));

        // And we sent the right request to Kanuu.
        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://kanuu.io/api/nonce'
                && $request->method() === 'POST';
        });
    }
}
