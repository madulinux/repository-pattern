<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Stubs\TestModel;
use Tests\Stubs\TestRepository;
use Illuminate\Database\Eloquent\Model;

class BaseRepositoryTest extends TestCase
{
    protected $repository;
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test model
        $this->model = TestModel::create(['name' => 'Test Model']);
        $this->repository = new TestRepository($this->model);
    }

    public function test_it_can_get_all_records()
    {
        $result = $this->repository->all();
        $this->assertCount(1, $result);
        $this->assertEquals('Test Model', $result->first()->name);
    }

    public function test_it_can_find_record_by_id()
    {
        $model = $this->repository->find($this->model->id);
        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals('Test Model', $model->name);
    }

    public function test_it_can_create_record()
    {
        $model = $this->repository->create([
            'name' => 'New Model'
        ]);

        $this->assertInstanceOf(Model::class, $model);
        $this->assertEquals('New Model', $model->name);
        $this->assertDatabaseHas('test_models', ['name' => 'New Model']);
    }

    public function test_it_can_update_record()
    {
        $result = $this->repository->update($this->model->id, [
            'name' => 'Updated Model'
        ]);

        $this->assertTrue($result);
        $this->assertDatabaseHas('test_models', ['name' => 'Updated Model']);
    }

    public function test_it_can_delete_record()
    {
        $result = $this->repository->delete($this->model->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('test_models', ['id' => $this->model->id]);
    }
}
