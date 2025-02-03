<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    protected $signature = 'make:repository  {name}';
    protected $description = 'Generate a new repository class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (file_exists($path)) {
            $this->error("Repositorie {$name} already exists!");
            return;
        }

        (new Filesystem)->ensureDirectoryExists(app_path('Repositories'));

        file_put_contents($path, $this->getStub($name));

        $this->info("Repository {$name} created successfully.");
    }

    protected function getStub($name): string
    {
        return <<<PHP
        <?php

        namespace App\Repositories;

        class {$name}
        {
            //
        }
        PHP;
    }
}
