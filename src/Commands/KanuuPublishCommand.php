<?php

namespace Kanuu\Laravel\Commands;

use Illuminate\Console\Command;

class KanuuPublishCommand extends Command
{
    protected $signature = 'kanuu:publish {--team?}';
    protected $description = 'Install Kanuu with some subscription boilerplate.';

    public function handle()
    {
        $this->line('');
    }
}
