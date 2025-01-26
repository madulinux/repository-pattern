<?php

namespace MaduLinux\RepositoryPattern\Traits;

trait CustomCacheKey
{
    /**
     * Custom cache key generator
     *
     * @var callable|null
     */
    protected $cacheKeyGenerator;

    /**
     * Set custom cache key generator
     *
     * @param callable $generator
     * @return self
     */
    public function setCacheKeyGenerator(callable $generator): self
    {
        $this->cacheKeyGenerator = $generator;
        return $this;
    }

    /**
     * Get cache key
     *
     * @param string $method
     * @param array $args
     * @return string
     */
    protected function getCacheKey(string $method, array $args = []): string
    {
        if ($this->cacheKeyGenerator) {
            return call_user_func($this->cacheKeyGenerator, $method, $args, $this->model);
        }

        $key = get_class($this->model) . ':' . $method;
        
        if (!empty($args)) {
            $key .= ':' . md5(serialize($args));
        }

        if (method_exists($this->model, 'getCacheVersion')) {
            $key .= ':v' . $this->model->getCacheVersion();
        }

        return $key;
    }

    /**
     * Get cache version
     *
     * @return string
     */
    protected function getCacheVersion(): string
    {
        if (method_exists($this->model, 'getCacheVersion')) {
            return $this->model->getCacheVersion();
        }

        return '1.0';
    }
}
