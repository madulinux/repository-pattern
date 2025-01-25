<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name : The name of the repository}
                          {--model= : The name of the model}
                          {--force : Force create the file}';

    protected $description = 'Create a new repository class';

    public function handle()
    {
        $name = $this->argument('name');
        $model = $this->option('model') ?? Str::singular($name);
        
        $this->createRepository($name, $model);
        $this->createInterface($name, $model);
        
        $this->info('Repository created successfully!');
    }

    protected function createRepository($name, $model)
    {
        $repositoryTemplate = File::exists($this->getStubPath('repository.stub'))
            ? File::get($this->getStubPath('repository.stub'))
            : File::get(__DIR__ . '/../../stubs/repository.stub');

        $repositoryContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelClass }}'],
            [$this->getNamespace($name), $this->getClassName($name), $model, class_basename($model)],
            $repositoryTemplate
        );

        $path = $this->getPath($name);

        if (File::exists($path) && !$this->option('force')) {
            $this->error('Repository already exists!');
            return;
        }

        File::makeDirectory(dirname($path), 0777, true, true);
        File::put($path, $repositoryContent);
    }

    protected function createInterface($name, $model)
    {
        $interfaceTemplate = File::exists($this->getStubPath('repository.interface.stub'))
            ? File::get($this->getStubPath('repository.interface.stub'))
            : File::get(__DIR__ . '/../../stubs/repository.interface.stub');

        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model }}'],
            [$this->getNamespace($name), $this->getClassName($name), $model],
            $interfaceTemplate
        );

        $path = $this->getInterfacePath($name);

        if (File::exists($path) && !$this->option('force')) {
            $this->error('Repository Interface already exists!');
            return;
        }

        File::makeDirectory(dirname($path), 0777, true, true);
        File::put($path, $interfaceContent);
    }

    protected function getStubPath($stub)
    {
        return base_path("stubs/repository-pattern/{$stub}");
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'] . '/Repositories/' . str_replace('\\', '/', $name) . 'Repository.php';
    }

    protected function getInterfacePath($name)
    {
        $name = Str::replaceFirst($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'] . '/Repositories/Interfaces/' . str_replace('\\', '/', $name) . 'RepositoryInterface.php';
    }

    protected function getNamespace($name)
    {
        return 'App\\Repositories\\' . str_replace('/', '\\', dirname(str_replace('\\', '/', $name)));
    }

    protected function getClassName($name)
    {
        return str_replace('/', '\\', $name);
    }
}
