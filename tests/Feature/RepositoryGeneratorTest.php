<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class RepositoryGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create temporary app directories if they don't exist
        $directories = [
            app_path('Models'),
            app_path('Repositories'),
            app_path('Repositories/Interfaces'),
            app_path('Services'),
            app_path('Services/Interfaces'),
            app_path('Http/Controllers'),
            app_path('Http/Controllers/Api'),
            app_path('Http/Resources'),
            app_path('Http/Requests'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }

    protected function tearDown(): void
    {
        // Cleanup generated files
        $directories = [
            app_path('Models/User.php'),
            app_path('Repositories/UserRepository.php'),
            app_path('Repositories/Interfaces/UserRepositoryInterface.php'),
            app_path('Services/UserService.php'),
            app_path('Services/Interfaces/UserServiceInterface.php'),
            app_path('Http/Controllers/UserController.php'),
            app_path('Http/Controllers/Api/UserController.php'),
            app_path('Http/Resources/UserResource.php'),
            app_path('Http/Requests/StoreUserRequest.php'),
            app_path('Http/Requests/UpdateUserRequest.php'),
        ];

        foreach ($directories as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_it_can_generate_repository_files()
    {
        $this->artisan('make:pattern', [
            'name' => 'User',
            '--all' => true,
            '--no-interaction' => true
        ])->assertExitCode(0);

        // Assert files were created
        $this->assertFileExists(app_path('Models/User.php'));
        $this->assertFileExists(app_path('Repositories/UserRepository.php'));
        $this->assertFileExists(app_path('Repositories/Interfaces/UserRepositoryInterface.php'));
        $this->assertFileExists(app_path('Services/UserService.php'));
        $this->assertFileExists(app_path('Services/Interfaces/UserServiceInterface.php'));
        $this->assertFileExists(app_path('Http/Controllers/UserController.php'));
        $this->assertFileExists(app_path('Http/Resources/UserResource.php'));
        $this->assertFileExists(app_path('Http/Requests/StoreUserRequest.php'));
        $this->assertFileExists(app_path('Http/Requests/UpdateUserRequest.php'));
    }

    public function test_it_can_generate_api_repository_files()
    {
        $this->artisan('make:pattern', [
            'name' => 'User',
            '--all' => true,
            '--api' => true,
            '--no-interaction' => true
        ])->assertExitCode(0);

        // Assert API controller was created
        $this->assertFileExists(app_path('Http/Controllers/Api/UserController.php'));
    }

    public function test_it_can_generate_specific_components()
    {
        $this->artisan('make:pattern', [
            'name' => 'User',
            '--repository' => true,
            '--service' => true,
            '--no-interaction' => true
        ])->assertExitCode(0);

        // Assert only specified files were created
        $this->assertFileExists(app_path('Repositories/UserRepository.php'));
        $this->assertFileExists(app_path('Repositories/Interfaces/UserRepositoryInterface.php'));
        $this->assertFileExists(app_path('Services/UserService.php'));
        $this->assertFileExists(app_path('Services/Interfaces/UserServiceInterface.php'));

        // Assert other files were not created
        $this->assertFileDoesNotExist(app_path('Models/User.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/UserController.php'));
        $this->assertFileDoesNotExist(app_path('Http/Resources/UserResource.php'));
    }
}
