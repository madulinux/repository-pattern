<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeCustomControllerCommand extends Command
{
    protected $signature = 'make:custom-controller {name : The name of the custom controller}
                          {--controller= : The name of the controller}
                          {--force : Force create the file}';

    protected $description = 'Create a new custom controller class';

    public function handle()
    {
        $name = $this->argument('name');
        $controller = $this->option('controller') ?? $name;

        $this->createCustomController($name, $controller);

        $this->info('Controller created successfully!');
    }

    protected function createCustomController($name, $controller)
    {
        $customControllerTemplate = File::exists($this->getStubPath('custom-controller.stub'))
            ? File::get($this->getStubPath('custom-controller.stub'))
            : File::get(__DIR__ . '/../../stubs/custom-controller.stub');

        $customControllerContent = str_replace(
            [
                '{{ namespace }}',
                '{{ class }}',
                '{{ controller }}',
                '{{ controllerClass }}',
                '{{ controllerVariable }}'
            ],
            [
                $this->getNamespace($name),
                $this->getClassName($name),
                $controller,
                class_basename($controller),
                lcfirst(class_basename($controller))
            ],
            $customControllerTemplate
        );

        $path = $this->getPath($name);

        if (File::exists($path) && !$this->option('force')) {
            $this->error('Controller already exists!');
            return;
        }

        File::makeDirectory(dirname($path), 0777, true, true);
        File::put($path, $customControllerContent);  
    }


    protected function getStubPath($stub)
    {
        return base_path("stubs/repository-pattern/{$stub}");
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->laravel->getNamespace(), '', $name);
        return $this->laravel['path'] . '/Http/Controllers/' . str_replace('\\', '/', $name) . 'Controller.php';
    }

    protected function getNamespace($name)
    {
        $namespace = 'App\\Http\\Controllers';

        // If name contains directory separators, append them to namespace
        $dirname = dirname(str_replace('\\', '/', $name));
        if ($dirname !== '.') {
            $namespace .= '\\' . str_replace('/', '\\', $dirname);
        }

        return $namespace;
    }

    protected function getClassName($name)
    {
        return str_replace('Controller', '', str_replace('/', '\\', $name));
    }
}
