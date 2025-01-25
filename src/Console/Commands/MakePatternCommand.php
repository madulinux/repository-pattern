<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePatternCommand extends Command
{
    protected $signature = 'make:pattern {name : The name of the pattern}
                          {--a|all : Generate a repository, service, model, migration, controller, resource, and requests}
                          {--r|repository : Generate a repository}
                          {--s|service : Generate a service}
                          {--m|model : Generate a model}
                          {--c|controller : Generate a controller}
                          {--resource : Generate a resource class}
                          {--requests : Generate request classes}
                          {--migration : Create a migration file}
                          {--api : Generate API controller}
                          {--force : Force create the files}';

    protected $description = 'Create a new pattern including repository, service, and other components';

    public function handle()
    {
        $name = $this->argument('name');

        // If no specific components are selected, default to repository and service
        if (!$this->option('all') && 
            !$this->option('repository') && 
            !$this->option('service') && 
            !$this->option('model') && 
            !$this->option('controller') && 
            !$this->option('resource') && 
            !$this->option('requests') && 
            !$this->option('migration')) {
            $this->option('repository') && $this->option('service');
        }

        if ($this->option('all')) {
            $this->createAll($name);
        } else {
            $this->createSelected($name);
        }

        $this->info('Pattern files generated successfully!');
    }

    protected function createAll($name)
    {
        // Generate Model and Migration
        if ($this->input->isInteractive() && $this->confirm("Do you want to generate a model?", true)) {
            $this->createModel($name);
        } else {
            $this->createModel($name);
        }

        // Generate Repository
        $this->createRepository($name);

        // Generate Service
        $this->createService($name);

        // Generate Controller
        if ($this->input->isInteractive() && $this->confirm("Do you want to generate a controller?", true)) {
            $this->createController($name);
        } else {
            $this->createController($name);
        }

        // Generate Resource
        if ($this->input->isInteractive() && $this->confirm("Do you want to generate a resource?", true)) {
            $this->createResource($name);
        } else {
            $this->createResource($name);
        }

        // Generate Requests
        if ($this->input->isInteractive() && $this->confirm("Do you want to generate request classes?", true)) {
            $this->createRequests($name);
        } else {
            $this->createRequests($name);
        }
    }

    protected function createSelected($name)
    {
        if ($this->option('model')) {
            $this->createModel($name);
        }

        if ($this->option('repository')) {
            $this->createRepository($name);
        }

        if ($this->option('service')) {
            $this->createService($name);
        }

        if ($this->option('resource')) {
            $this->createResource($name);
        }

        if ($this->option('requests')) {
            $this->createRequests($name);
        }

        if ($this->option('controller')) {
            $this->createController($name);
        }
    }

    protected function createModel($name)
    {
        $params = ['name' => $name];
        if ($this->option('force')) {
            $params['--force'] = true;
        }
        if ($this->option('migration')) {
            $params['--migration'] = true;
        }

        $this->call('make:model', $params);
    }

    protected function createRepository($name)
    {
        $params = ['name' => $name];
        if ($this->option('force')) {
            $params['--force'] = true;
        }
        if ($this->option('model')) {
            $params['--model'] = $name;
        }

        $this->call('make:repository', $params);
    }

    protected function createService($name)
    {
        $params = ['name' => $name];
        if ($this->option('force')) {
            $params['--force'] = true;
        }
        if ($this->option('repository')) {
            $params['--repository'] = $name;
        }

        $this->call('make:service', $params);
    }

    protected function createResource($name)
    {
        $params = ['name' => $name . 'Resource'];
        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->call('make:resource', $params);
    }

    protected function createRequests($name)
    {
        // Create Store Request
        $params = ['name' => 'Store' . $name . 'Request'];
        if ($this->option('force')) {
            $params['--force'] = true;
        }
        $this->call('make:request', $params);

        // Create Update Request
        $params['name'] = 'Update' . $name . 'Request';
        $this->call('make:request', $params);
    }

    protected function createController($name)
    {
        $params = [
            'name' => $this->option('api') 
                ? 'Api/' . $name . 'Controller'
                : $name . 'Controller'
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        if ($this->option('api')) {
            $params['--api'] = true;
        }

        if ($this->option('resource')) {
            $params['--resource'] = true;
        }

        $this->call('make:controller', $params);
    }
}
