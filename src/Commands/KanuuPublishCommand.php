<?php

namespace Kanuu\Laravel\Commands;

use Illuminate\Console\GeneratorCommand;

class KanuuPublishCommand extends GeneratorCommand
{
    protected $signature = 'kanuu:publish {--team : Attach subscription to teams instead of users}';
    protected $description = 'Install Kanuu with some subscription boilerplate.';

    public function handle()
    {
        $this->line('');
    }

    protected function getStub()
    {
        return '';
    }
}
