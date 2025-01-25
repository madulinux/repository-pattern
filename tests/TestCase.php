<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use MaduLinux\RepositoryPattern\RepositoryPatternServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            RepositoryPatternServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup cache
        $app['config']->set('cache.default', 'array');
        $app['config']->set('cache.stores.array', [
            'driver' => 'array',
        ]);
    }

    protected function setUpDatabase()
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
}
