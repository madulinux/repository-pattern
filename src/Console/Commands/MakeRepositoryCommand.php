<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $modelName = $this->option('model') ?? Str::singular($name);

        $this->createRepository($name, $modelName);
        $this->createInterface($name);

        $this->info('Repository created successfully.');
    }

    /**
     * Create repository class
     *
     * @param string $name
     * @param string $modelName
     * @return void
     */
    protected function createRepository(string $name, string $modelName): void
    {
        $repositoryTemplate = File::get(__DIR__ . '/../../stubs/repository.stub');

        $repositoryTemplate = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelClass }}'],
            [
                'App\Repositories',
                $name,
                $modelName,
                $modelName
            ],
            $repositoryTemplate
        );

        if (!File::exists($path = app_path('Repositories'))) {
            File::makeDirectory($path, 0777, true);
        }

        File::put(app_path("Repositories/{$name}Repository.php"), $repositoryTemplate);
    }

    /**
     * Create repository interface
     *
     * @param string $name
     * @return void
     */
    protected function createInterface(string $name): void
    {
        $interfaceTemplate = File::get(__DIR__ . '/../../stubs/repository.interface.stub');

        $interfaceTemplate = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\Repositories', $name],
            $interfaceTemplate
        );

        if (!File::exists($path = app_path('Repositories/Interfaces'))) {
            File::makeDirectory($path, 0777, true);
        }

        File::put(app_path("Repositories/Interfaces/{$name}RepositoryInterface.php"), $interfaceTemplate);
    }
}
