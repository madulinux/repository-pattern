<?php

namespace MaduLinux\RepositoryPattern\Traits;

trait HasEvents
{
    /**
     * Event handlers
     *
     * @var array
     */
    protected $events = [
        'creating' => [],
        'created' => [],
        'updating' => [],
        'updated' => [],
        'deleting' => [],
        'deleted' => [],
    ];

    /**
     * Register an event handler
     *
     * @param string $event
     * @param callable $handler
     * @return self
     */
    public function on(string $event, callable $handler): self
    {
        if (isset($this->events[$event])) {
            $this->events[$event][] = $handler;
        }
        return $this;
    }

    /**
     * Trigger an event
     *
     * @param string $event
     * @param mixed $payload
     * @return void
     */
    protected function trigger(string $event, $payload = null): void
    {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $handler) {
                $handler($payload);
            }
        }
    }
}
