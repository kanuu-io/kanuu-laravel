<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Kanuu\Laravel\KanuuServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            KanuuServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('kanuu.api_key', 'some_kanuu_api_key');
    }

    protected function mockKanuuHttpCall($mockedData = [])
    {
        Http::fake(['kanuu.io/*' => Http::response($mockedData, 200)]);
    }

    protected function assertNonceCallWasSent()
    {
        $this->assertHttpCallWasSent('POST', 'https://kanuu.io/api/nonce');
    }

    protected function assertHttpCallWasSent(string $method, string $url)
    {
        Http::assertSent(function (Request $request) use ($method, $url) {
            return $request->url() === $url
                && $request->method() === $method;
        });
    }
}
