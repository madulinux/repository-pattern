<?php

namespace MaduLinux\RepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:criteria {name : The name of the criteria}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        
        $this->createCriteria($name);

        $this->info('Criteria created successfully.');
    }

    /**
     * Create criteria class
     *
     * @param string $name
     * @return void
     */
    protected function createCriteria(string $name): void
    {
        $criteriaTemplate = File::get(__DIR__ . '/../../stubs/criteria.stub');

        $criteriaTemplate = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            ['App\Criteria', $name],
            $criteriaTemplate
        );

        if (!File::exists($path = app_path('Criteria'))) {
            File::makeDirectory($path, 0777, true);
        }

        File::put(app_path("Criteria/{$name}Criteria.php"), $criteriaTemplate);
    }
}
