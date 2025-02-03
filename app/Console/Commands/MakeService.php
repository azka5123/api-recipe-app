<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Generate a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (file_exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        (new Filesystem)->ensureDirectoryExists(app_path('Services'));

        file_put_contents($path, $this->getStub($name));

        $this->info("Service {$name} created successfully.");
    }

    protected function getStub($name): string
    {
        return <<<PHP
        <?php

        namespace App\Services;

        class {$name}
        {
            //
        }
        PHP;
    }
}
