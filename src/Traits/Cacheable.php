<?php

namespace MaduLinux\RepositoryPattern\Traits;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

trait Cacheable
{
    /**
     * Cache time in minutes
     *
     * @var int
     */
    protected $cacheTime = 60;

    /**
     * Cache tags
     *
     * @var array
     */
    protected $cacheTags = [];

    /**
     * Enable or disable cache
     *
     * @var bool
     */
    protected $cacheEnabled = true;

    /**
     * Get cache key
     *
     * @param string $method
     * @param array $args
     * @return string
     */
    protected function getCacheKey(string $method, array $args = []): string
    {
        $key = get_class($this) . '@' . $method;
        
        if (!empty($args)) {
            $key .= ':' . md5(serialize($args));
        }

        return $key;
    }

    /**
     * Get cache tags
     *
     * @return array
     */
    protected function getCacheTags(): array
    {
        $modelClass = get_class($this->model);
        return array_merge([$modelClass], $this->cacheTags);
    }

    /**
     * Remember data in cache
     *
     * @param string $method
     * @param array $args
     * @param \Closure $callback
     * @return mixed
     */
    protected function remember(string $method, array $args, \Closure $callback)
    {
        if (!$this->cacheEnabled) {
            return $callback();
        }

        $key = $this->getCacheKey($method, $args);
        $tags = $this->getCacheTags();

        if (empty($tags)) {
            return Cache::remember($key, Carbon::now()->addMinutes($this->cacheTime), $callback);
        }

        return Cache::tags($tags)->remember($key, Carbon::now()->addMinutes($this->cacheTime), $callback);
    }

    /**
     * Flush cache
     *
     * @return void
     */
    public function flushCache(): void
    {
        $tags = $this->getCacheTags();
        
        if (empty($tags)) {
            Cache::flush();
            return;
        }

        Cache::tags($tags)->flush();
    }

    /**
     * Enable cache
     *
     * @return self
     */
    public function enableCache(): self
    {
        $this->cacheEnabled = true;
        return $this;
    }

    /**
     * Disable cache
     *
     * @return self
     */
    public function disableCache(): self
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * Set cache time
     *
     * @param int $minutes
     * @return self
     */
    public function setCacheTime(int $minutes): self
    {
        $this->cacheTime = $minutes;
        return $this;
    }

    /**
     * Set cache tags
     *
     * @param array $tags
     * @return self
     */
    public function setCacheTags(array $tags): self
    {
        $this->cacheTags = $tags;
        return $this;
    }
}
