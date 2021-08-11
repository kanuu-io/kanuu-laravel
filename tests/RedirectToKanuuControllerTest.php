<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Database\Eloquent\Model;
use Kanuu\Laravel\Facades\Kanuu;
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
        $this->mockKanuuHttpCall([
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ]);

        // When we access that route with an identifier.
        $response = $this->get('/kanuu/some_identifier');

        // Then we have been redirected to the right URL.
        $response->assertStatus(302);
        $response->assertRedirect('https://kanuu.io/manage/some_team/some_nonce');

        // And we got that URL from Kanuu.
        $this->assertNonceCallWasSent();
    }

    /** @test */
    public function it_works_when_an_eloquent_model_is_given_as_route_parameter()
    {
        // Given the following named route pointing to the RedirectToKanuu controller.
        $this->app->make('router')
            ->get('/kanuu/{identifier}', RedirectToKanuu::class)
            ->name('kanuu.redirect');

        // And an Eloquent model with a route key.
        $model = new class() extends Model {
            public function getRouteKey()
            {
                return 'some_model_identifier';
            }
        };

        // And the following mocked response.
        $this->mockKanuuHttpCall([
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ]);

        // When we access that route and provide the model as a parameter.
        $response = $this->get(route('kanuu.redirect', $model));

        // Then we have been redirected to the right URL.
        $response->assertStatus(302);
        $response->assertRedirect('https://kanuu.io/manage/some_team/some_nonce');

        // And we got that URL from Kanuu.
        $this->assertNonceCallWasSent();
    }

    /** @test */
    public function it_provides_a_route_helper_to_redirect_to_kanuu()
    {
        // Given we use the `redirectRoute` method to define our route.
        Kanuu::redirectRoute();

        // And the following mocked response.
        $this->mockKanuuHttpCall([
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ]);

        // When we access that route with an identifier.
        $response = $this->get('/kanuu/some_identifier');

        // Then we have been redirected to the right URL.
        $response->assertStatus(302);
        $response->assertRedirect('https://kanuu.io/manage/some_team/some_nonce');

        // And we got that URL from Kanuu.
        $this->assertNonceCallWasSent();
    }
}
