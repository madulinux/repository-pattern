<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tests\Stubs\TestModel;
use Tests\Stubs\TestRepository;
use Illuminate\Support\Facades\Cache;

class CacheableTest extends TestCase
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

    public function test_it_can_cache_data()
    {
        // First call should cache the data
        $result1 = $this->repository->all();
        
        // Second call should retrieve from cache
        $result2 = $this->repository->all();
        
        $this->assertEquals($result1, $result2);
        $this->assertTrue(Cache::has($this->repository->getCacheKeyTest('all')));
    }

    public function test_it_can_flush_cache()
    {
        // Cache some data
        $this->repository->all();
        
        // Flush the cache
        $this->repository->flushCache();
        
        $this->assertFalse(Cache::has($this->repository->getCacheKeyTest('all')));
    }

    public function test_it_can_disable_and_enable_cache()
    {
        // Disable cache
        $this->repository->disableCache();
        
        // This should not be cached
        $this->repository->all();
        
        $this->assertFalse(Cache::has($this->repository->getCacheKeyTest('all')));
        
        // Enable cache
        $this->repository->enableCache();
        
        // This should be cached
        $this->repository->all();
        
        $this->assertTrue(Cache::has($this->repository->getCacheKeyTest('all')));
    }

    public function test_it_can_set_cache_time()
    {
        $this->repository->setCacheTime(120);
        $this->assertEquals(120, $this->repository->getCacheTime());
    }

    public function test_it_can_set_cache_tags()
    {
        $tags = ['tag1', 'tag2'];
        $this->repository->setCacheTags($tags);
        $this->assertEquals($tags, $this->repository->getCacheTags());
    }
}
