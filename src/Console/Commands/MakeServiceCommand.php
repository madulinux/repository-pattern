<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name : The name of the service}
                          {--repository= : The name of the repository}
                          {--force : Force create the file}';

    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $repository = $this->option('repository') ?? $name;

        $this->createService($name, $repository);
        // $this->createInterface($name, $repository);

        $this->info('Service created successfully!');
    }

    protected function createService($name, $repository)
    {
        $serviceTemplate = File::exists($this->getStubPath('service.stub'))
            ? File::get($this->getStubPath('service.stub'))
            : File::get(__DIR__ . '/../../stubs/service.stub');

        $serviceContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ repository }}',
                '{{ repositoryClass }}',
                '{{ repositoryVariable }}'
            ],
            [
                $this->getNamespace($name),
                $this->getClassName($name),
                $repository,
                class_basename($repository),
                lcfirst(class_basename($repository))
            ],
            $serviceTemplate
        );

        $path = $this->getPath($name);

        if (File::exists($path) && !$this->option('force')) {
            $this->error('Service already exists!');
            return;
        }

        File::makeDirectory(dirname($path), 0777, true, true);
        File::put($path, $serviceContent);
    }

    protected function createInterface($name, $repository)
    {
        $interfaceTemplate = File::exists($this->getStubPath('service.interface.stub'))
            ? File::get($this->getStubPath('service.interface.stub'))
            : File::get(__DIR__ . '/../../stubs/service.interface.stub');

        $interfaceContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ repository }}'],
            [$this->getNamespace($name), $this->getClassName($name), $repository],
            $interfaceTemplate
        );

        $path = $this->getInterfacePath($name);

        if (File::exists($path) && !$this->option('force')) {
            $this->error('Service Interface already exists!');
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
        return $this->laravel['path'] . '/Services/' . str_replace('\\', '/', $name) . 'Service.php';
    }

    protected function getInterfacePath($name)
    {
        $name = Str::replaceFirst($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'] . '/Services/Interfaces/' . str_replace('\\', '/', $name) . 'ServiceInterface.php';
    }

    protected function getNamespace($name)
    {
        $namespace = 'App\\Services';

        // If name contains directory separators, append them to namespace
        $dirname = dirname(str_replace('\\', '/', $name));
        if ($dirname !== '.') {
            $namespace .= '\\' . str_replace('/', '\\', $dirname);
        }

        return $namespace;
    }

    protected function getClassName($name)
    {
        return str_replace('/', '\\', $name);
    }
}
