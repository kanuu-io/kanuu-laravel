<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Support\Facades\Http;
use Kanuu\Laravel\RedirectToKanuu;

/**
 * @see RedirectToKanuu
 */
class RedirectToKanuuControllerTest extends TestCase
{
    /** @test */
    public function it_provides_a_controller_that_redirects_to_kanuu()
    {
        // Given the following route pointing to the RedirectToKanuu controller.
        $this->app->make('router')->get('/kanuu/{identifier}', RedirectToKanuu::class);

        // And the following mocked response.
        $mockedResponse = [
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ];

        // Used by the fake Http facade.
        Http::fake(['*' => Http::response($mockedResponse, 200)]);

        // When we access that route with an identifier.
        $response = $this->get('/kanuu/some_identifier');

        // Then we have been redirected to the right URL.
        $response->assertStatus(302);
        $response->assertRedirect('https://kanuu.io/manage/some_team/some_nonce');
    }
}
