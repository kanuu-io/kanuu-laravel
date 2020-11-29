<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Support\Facades\Artisan;
use Kanuu\Laravel\Commands\KanuuPublishCommand;

/**
 * @see KanuuPublishCommand
 */
class KanuuPublishCommandTest extends TestCase
{
    /** @test */
    public function dummy()
    {
        $this->artisan('kanuu:publish')
            ->expectsOutput('')
            ->run();
    }
}
