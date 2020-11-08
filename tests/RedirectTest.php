<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Http\RedirectResponse;
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
        $this->mockKanuuHttpCall([
            'nonce' => 'some_nonce',
            'url' => 'https://kanuu.io/manage/some_team/some_nonce',
        ]);

        // When we redirect to Kanuu with some identifier.
        $response = Kanuu::redirect('some_identifier');

        // Then we returned a RedirectResponse towards the URL provided in the mocked response.
        $this->assertTrue($response instanceof RedirectResponse);
        $this->assertTrue($response->isRedirect('https://kanuu.io/manage/some_team/some_nonce'));

        // And we got that URL from Kanuu.
        $this->assertKanuuHttpCallWasSent();
    }
}
