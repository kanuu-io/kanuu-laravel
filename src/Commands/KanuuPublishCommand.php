<?php

namespace Kanuu\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class KanuuPublishCommand extends GeneratorCommand
{
    protected $signature = 'kanuu:publish {--team : Attach subscription to teams instead of users}';
    protected $description = 'Install Kanuu with some subscription boilerplate.';

    public function handle()
    {
        $this->call('make:model', [
            'name' => 'Subscription',
            '--migration' => true,
            '--factory' => true,
        ]);

        // $this->line(
        //     $this->qualifyClass('Providers/KanuuServiceProvider')
        // );
    }

    protected function getStub()
    {
        return '';
    }

    protected function createStub($rawName, $stubFile): bool
    {
        $name = $this->qualifyClass($rawName);

        if (! $this->isForced() && $this->alreadyExists($rawName)) {
            $this->error($name . ' already exists!');

            return false;
        }

        $path = $this->getPath($name);
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildStub($name, $stubFile));

        $this->info($this->type.' created successfully.');

        return true;
    }

    protected function buildStub($name, $stubFile)
    {
        $stub = $this->files->get($stubFile);
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        $stub = $this->sortImports($stub);

        return $stub;
    }

    protected function isForced()
    {
        return $this->hasOption('force') && $this->option('force');
    }
}
