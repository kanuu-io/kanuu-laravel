<?php

namespace Kanuu\Laravel\Tests;

use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Kanuu\Laravel\Commands\KanuuPublishCommand;

/**
 * @see KanuuPublishCommand
 */
class KanuuPublishCommandTest extends TestCase
{
    /** @var string */
    protected static $tmpDir = __DIR__ . '/tmp';

    /** @var Filesystem  */
    protected $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->files->deleteDirectory(static::$tmpDir);
        $this->files->copyDirectory($this->app->basePath(), static::$tmpDir);
        $this->app->setBasePath(static::$tmpDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->files->deleteDirectory(static::$tmpDir);
    }

    /** @test */
    public function dummy()
    {
        Artisan::call('kanuu:publish');
        // $this->artisan('kanuu:publish');

        $this->assertBasePathFileExists('app/models/Subscription.php');
        $this->assertBasePathFileExists('database/factories/SubscriptionFactory.php');
        $this->assertContainsEquals('create_subscriptions_table', $this->getMigrations());

        dd(Artisan::output());
    }

    protected function assertBasePathFileExists($filename)
    {
        $this->assertFileExists($this->app->basePath($filename));
    }

    protected function getMigrations(): Collection
    {
        $filenames = $this->files->glob(
            $this->app->basePath('database/migrations/*.php')
        );

        return collect($filenames)->map(function ($filename) {
            return (string) Str::of($filename)->afterLast('/')->substr(18, -4);
        });
    }
}
