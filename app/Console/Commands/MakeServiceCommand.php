<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name : The name of the service}';
    protected $description = 'Create a new service class';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        // Check if the service already exists
        if ($this->files->exists($path)) {
            $this->error("Service {$name} already exists!");
            return Command::FAILURE;
        }

        // Ensure the Services directory exists
        $this->files->ensureDirectoryExists(app_path('Services'));

        // Create the service file with basic content
        $stub = $this->getServiceStub($name);
        $this->files->put($path, $stub);

        $this->info("Service {$name} created successfully.");
        return Command::SUCCESS;
    }

    protected function getServiceStub($name)
    {
        return <<<EOT
<?php

namespace App\Services;

class {$name}
{
    public function __construct()
    {
        // Initialize service dependencies here if needed
    }

    // Add your service methods here
}
EOT;
    }
}
