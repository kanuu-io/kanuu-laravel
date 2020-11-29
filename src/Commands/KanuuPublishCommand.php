<?php

namespace Kanuu\Laravel\Commands;

use Closure;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class KanuuPublishCommand extends GeneratorCommand
{
    protected $signature = 'kanuu:publish {--team : Attach subscription to teams instead of users}';
    protected $description = 'Install Kanuu with some subscription boilerplate.';

    public function handle()
    {
        $subsctiptionModelStub = $this->isLaravel7()
            ? 'subscription_model_l7'
            : 'subscription_model_l8';

        $this->createModelIfMissing($this->isTeam() ? 'Team' : 'User');
        $this->createModel('Subscription', $subsctiptionModelStub);
        $this->createFactory('Subscription');
        $this->createMigration('create_subscriptions_table', 'subscription_migration');
        $this->createClass('Providers/KanuuServiceProvider', 'kanuu_service_provider', 'KanuuServiceProvider');
        $this->createClass('Concerns/HasSubscriptions', 'has_subscription', 'HasSubscriptions trait');
        $this->registerProvider('KanuuServiceProvider');
        $this->addTraitToModel($this->isTeam() ? 'Team' : 'User', $this->qualifyClass('Concerns/HasSubscriptions'));
        $this->addKanuuRoutes();

        $this->comment('Done! Happy billing! 💸');
    }

    protected function createModelIfMissing(string $model)
    {
        if ($this->files->exists($this->getPath($this->qualifyModel($model)))) {
            return;
        }

        if (! $this->confirm("You have no $model model, should we create one for you?", true)) {
            return;
        }

        $this->callSilent('make:model', [
            'name' => $model,
            '--migration' => true,
            '--factory' => true,
        ]);
    }

    protected function createModel($rawName, $stubName): bool
    {
        $name = $this->qualifyModel($rawName);
        $path = $this->getPath($name);
        $stub = $this->buildStub($stubName, $name);

        return $this->writeStub($rawName . ' model', $path, $stub);
    }

    protected function createClass(string $rawName, string $stubName, ?string $entity = null): bool
    {
        $name = $this->qualifyClass($rawName);
        $path = $this->getPath($name);
        $stub = $this->buildStub($stubName, $name);

        return $this->writeStub($entity ?? $rawName, $path, $stub);
    }

    protected function createMigration(string $migrationName, string $stubName)
    {
        $path = $this->laravel->databasePath(sprintf(
            'migrations/%s_%s.php',
            date('Y_m_d_His'),
            $migrationName
        ));

        $stub = $this->buildStub($stubName);

        return $this->writeStub($migrationName, $path, $stub);
    }

    protected function buildStub(string $stubName, ?string $className = null)
    {
        $stub = $this->files->get(
            __DIR__ . '/stubs/' . $stubName . '.stub'
        );

        if ($className) {
            $stub = $this->replaceNamespace($stub, $className)
                ->replaceClass($stub, $className);
        }

        foreach ($this->getExtraReplacements() as $from => $to) {
            $stub = str_replace('{{' . $from . '}}', $to, $stub);
        }

        return $this->sortImports($stub);
    }

    protected function writeStub($entity, $path, $stub): bool
    {
        if (! $this->isForced() && $this->files->exists($path)) {
            $this->error($entity . ' already exists!');

            return false;
        }

        $this->makeDirectory($path);
        $this->files->put($path, $stub);

        return true;
    }

    protected function getExtraReplacements(): array
    {
        return [
            'modelsNamespace' => $this->qualifyModel(''),
            'billableEntity' => $this->isTeam() ? 'team' : 'user',
            'billableEntityClass' => $this->isTeam() ? 'Team' : 'User',
            'billableEntityTable' => $this->isTeam() ? 'teams' : 'users',
            'billableEntityId' => $this->isTeam() ? 'team_id' : 'user_id',
            'billableEntityFullClass' => $this->qualifyModel($this->isTeam() ? 'Team' : 'User'),
        ];
    }

    protected function createFactory(string $model): bool
    {
        $success = ! $this->callSilent('make:factory', [
            'name' => $model . 'Factory',
            '--model' => $this->qualifyModel($model),
        ]);

        if (! $success) {
            $this->error($model . ' factory already exists!');
        }

        return $success;
    }

    protected function registerProvider(string $provider)
    {
        $this->updateBaseFile('config/app.php', function ($content) use ($provider) {
            return preg_replace(
                '/(\/\*[\s*]*Package Service Providers...[\s*]*\*\/)/',
                sprintf("$1\n        %s::class,", $this->qualifyClass('Providers/' . $provider)),
                $content
            );
        });
    }

    protected function addTraitToModel(string $model, string $trait)
    {
        $this->updateFile($this->getPath($this->qualifyModel($model)), function ($content) use ($trait) {
            $content = preg_replace(
                '/(class[^{]*{)/',
                sprintf("$1\n    use %s;", class_basename($trait)),
                $content
            );
            $content = preg_replace(
                '/(use Illuminate\\\\Database\\\\Eloquent\\\\Model;)/',
                sprintf("$1\nuse %s;", $trait),
                $content
            );

            return $this->sortImports($content);
        });
    }

    protected function addKanuuRoutes()
    {
        $this->updateBaseFile('routes/web.php', function ($content) {
            $content = preg_replace(
                '/(use Illuminate\\\\Support\\\\Facades\\\\Route;)/',
                "$1\nuse Kanuu\Laravel\Facades\Kanuu;",
                $content
            );

            $content = trim($content) . "\n\n";
            $content .= "Kanuu::redirectRoute()->name('kanuu.redirect');\n";
            $content .= "Kanuu::webhookRoute()->name('webhooks.paddle');\n";

            return $this->sortImports($content);
        });
    }

    protected function updateBaseFile(string $path, Closure $callback)
    {
        $path = $this->laravel->basePath($path);

        return $this->updateFile($path, $callback);
    }

    protected function updateFile(string $path, Closure $callback)
    {
        if (! $this->files->exists($path)) {
            return;
        }

        $content = $this->files->get($path);
        $content = $callback($content);
        $this->files->replace($path, $content);
    }

    protected function isForced()
    {
        return $this->hasOption('force') && $this->option('force');
    }

    protected function isTeam()
    {
        return $this->hasOption('team') && $this->option('team');
    }

    protected function isLaravel7()
    {
        return Str::before($this->laravel->version(), '.') === '7';
    }

    protected function getStub()
    {
        return ''; // Ignored.
    }
}
