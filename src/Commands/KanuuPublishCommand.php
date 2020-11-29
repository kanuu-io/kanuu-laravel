<?php

namespace Kanuu\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class KanuuPublishCommand extends GeneratorCommand
{
    protected $signature = 'kanuu:publish {--team : Attach subscription to teams instead of users}';
    protected $description = 'Install Kanuu with some subscription boilerplate.';

    public function handle()
    {
        $this->createModel('Subscription', 'subscription_model_l8');
        $this->callSilent('make:factory', [
            'name' => 'SubscriptionFactory',
            '--model' => $this->qualifyClass('Subscription'),
        ]);
        $this->comment('Subscription factory create successfully.');

        $this->createMigration('create_subscriptions_table', 'subscription_migration');


        $this->createClass('Providers/KanuuServiceProvider', 'kanuu_service_provider', 'KanuuServiceProvider');
        $this->createClass('Concerns/HasSubscriptions', 'has_subscription', 'HasSubscriptions trait');
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
            $stub = str_replace($from, $to, $stub);
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

        $this->info($entity . ' created successfully.');

        return true;
    }

    protected function isForced()
    {
        return $this->hasOption('force') && $this->option('force');
    }

    protected function getExtraReplacements(): array
    {
        return [
            'modelsNamespace' => $this->qualifyModel('')
        ];
    }

    protected function getStub()
    {
        return ''; // Ignored.
    }
}
