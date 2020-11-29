<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kanuu\Laravel\Commands\KanuuPublishCommand;

/**
 * @see KanuuPublishCommand
 */
class KanuuPublishCommandTest extends TestCase
{
    /** @var string */
    protected static $tmpDir = __DIR__ . '/tmp';

    /** @var Filesystem */
    protected $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->files->deleteDirectory(static::$tmpDir);
        $this->files->copyDirectory($this->app->basePath(), static::$tmpDir);
        $this->files->makeDirectory(static::$tmpDir . '/routes');
        $this->files->put(static::$tmpDir . '/routes/web.php', $this->getRoutesFileContent());
        $this->files->makeDirectory(static::$tmpDir . '/app/Http/Middleware', 0755, true, true);
        $this->files->put(static::$tmpDir . '/app/Http/Middleware/VerifyCsrfToken.php', $this->getVerifyCsrfTokenContent());
        $this->files->makeDirectory(static::$tmpDir . '/app/Models', 0755, true, true);
        $this->app->setBasePath(static::$tmpDir);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->files->deleteDirectory(static::$tmpDir);
    }

    /** @test */
    public function it_publish_the_subscription_model_for_a_user()
    {
        // When we publish Kanuu's subscription boilerplate for a user.
        $this->artisan('kanuu:publish')
            ->expectsConfirmation('You have no User model, should we create one for you?', 'yes')
            ->expectsOutput('Done! Happy billing! 💸')
            ->run();

        // Then we created the Subscription's model, factory and migration.
        $migrations = $this->getMigrations();
        $this->assertBasePathFileExists('app/Models/Subscription.php');
        $this->assertBasePathFileExists('database/factories/SubscriptionFactory.php');
        $this->assertContainsEquals('create_subscriptions_table', $migrations->keys());

        // And the Subscription is attached to a user.
        $this->assertBaseFileContains('public function user()', 'app/models/Subscription.php');
        $this->assertBaseFileContains('$this->belongsTo(User::class);', 'app/models/Subscription.php');

        // And the migration references the users table.
        $this->assertFileContains(
            '$table->foreignId(\'user_id\')->nullable()->constrained(\'users\')->nullOnDelete();',
            $migrations->get('create_subscriptions_table')
        );

        // And we now have a User's model, factory and migration.
        $this->assertBasePathFileExists('app/Models/User.php');
        $this->assertBasePathFileExists('database/factories/UserFactory.php');
        $this->assertContainsEquals('create_users_table', $migrations->keys());

        // And that user is using the HasSubscription trait.
        $this->assertBaseFileContains('use App\Concerns\HasSubscriptions;', 'app/models/User.php');
        $this->assertBaseFileContains('use HasSubscriptions;', 'app/models/User.php');

        // And a new KanuuServiceProvider was created and registered.
        $this->assertBasePathFileExists('app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('App\Providers\KanuuServiceProvider::class,', 'config/app.php');

        // And that KanuuServiceProvider is attaching subscriptions to Users.
        $this->assertBaseFileContains('use App\Models\User;', 'app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('User::findOrFail($identifier);', 'app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('$user->subscriptions()->updateOrCreate', 'app/Providers/KanuuServiceProvider.php');

        // And Kanuu routes have been added to the routes/web.php file.
        $this->assertBaseFileContains('use Kanuu\Laravel\Facades\Kanuu;', 'routes/web.php');
        $this->assertBaseFileContains('Kanuu::redirectRoute()->name(\'kanuu.redirect\');', 'routes/web.php');
        $this->assertBaseFileContains('Kanuu::webhookRoute()->name(\'webhooks.paddle\');', 'routes/web.php');

        // And the "webhooks/*" pattern was added to the VerifyCsrfToken exceptions.
        $this->assertBaseFileContains("'webhooks/*',", 'app/Http/Middleware/VerifyCsrfToken.php');
    }

    /** @test */
    public function it_publish_the_subscription_model_for_a_team()
    {
        // When we publish Kanuu's subscription boilerplate for a team.
        $this->artisan('kanuu:publish', ['--team' => true])
            ->expectsConfirmation('You have no Team model, should we create one for you?', 'yes')
            ->expectsOutput('Done! Happy billing! 💸')
            ->run();

        // Then we created the Subscription's model, factory and migration.
        $migrations = $this->getMigrations();
        $this->assertBasePathFileExists('app/Models/Subscription.php');
        $this->assertBasePathFileExists('database/factories/SubscriptionFactory.php');
        $this->assertContainsEquals('create_subscriptions_table', $migrations->keys());

        // And the Subscription is attached to a team.
        $this->assertBaseFileContains('public function team()', 'app/models/Subscription.php');
        $this->assertBaseFileContains('$this->belongsTo(Team::class);', 'app/models/Subscription.php');

        // And the migration references the teams table.
        $this->assertFileContains(
            '$table->foreignId(\'team_id\')->nullable()->constrained(\'teams\')->nullOnDelete();',
            $migrations->get('create_subscriptions_table')
        );

        // And we now have a Team's model, factory and migration.
        $this->assertBasePathFileExists('app/Models/Team.php');
        $this->assertBasePathFileExists('database/factories/TeamFactory.php');
        $this->assertContainsEquals('create_teams_table', $migrations->keys());

        // And that team is using the HasSubscription trait.
        $this->assertBaseFileContains('use App\Concerns\HasSubscriptions;', 'app/models/Team.php');
        $this->assertBaseFileContains('use HasSubscriptions;', 'app/models/Team.php');

        // And a new KanuuServiceProvider was created and registered.
        $this->assertBasePathFileExists('app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('App\Providers\KanuuServiceProvider::class,', 'config/app.php');

        // And that KanuuServiceProvider is attaching subscriptions to Teams.
        $this->assertBaseFileContains('use App\Models\Team;', 'app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('Team::findOrFail($identifier);', 'app/Providers/KanuuServiceProvider.php');
        $this->assertBaseFileContains('$team->subscriptions()->updateOrCreate', 'app/Providers/KanuuServiceProvider.php');

        // And the "webhooks/*" pattern was added to the VerifyCsrfToken exceptions.
        $this->assertBaseFileContains("'webhooks/*',", 'app/Http/Middleware/VerifyCsrfToken.php');
    }

    protected function assertBasePathFileExists($filename)
    {
        $this->assertFileExists($this->app->basePath($filename));
    }

    protected function assertBaseFileContains(string $expected, string $filename)
    {
        $this->assertFileContains($expected, $this->app->basePath($filename));
    }

    protected function assertFileContains(string $expected, string $filename)
    {
        $this->assertStringContainsString($expected, $this->files->get($filename));
    }

    protected function getMigrations(): Collection
    {
        $filenames = $this->files->glob(
            $this->app->basePath('database/migrations/*.php')
        );

        return collect($filenames)->mapWithKeys(function ($filename) {
            $key = (string) Str::of($filename)->afterLast('/')->substr(18, -4);

            return [$key => $filename];
        });
    }

    protected function getRoutesFileContent()
    {
        return <<<EOL
        <?php

        use Illuminate\Support\Facades\Route;

        Route::get('/', function () {
            return view('welcome');
        });
        EOL;
    }

    protected function getVerifyCsrfTokenContent()
    {
        return <<<EOL
        <?php

        namespace App\Http\Middleware;

        use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

        class VerifyCsrfToken extends Middleware
        {
            protected \$except = [
                //
            ];
        }
        EOL;
    }
}
