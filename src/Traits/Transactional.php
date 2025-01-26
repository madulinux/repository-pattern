<?php

namespace MaduLinux\RepositoryPattern\Traits;

use Illuminate\Support\Facades\DB;
use Closure;
use Throwable;

trait Transactional
{
    /**
     * Execute a callback within a transaction
     *
     * @param Closure $callback
     * @return mixed
     * @throws Throwable
     */
    public function transaction(Closure $callback)
    {
        return DB::transaction($callback);
    }

    /**
     * Start a new database transaction
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit the active database transaction
     *
     * @return void
     */
    public function commit(): void
    {
        DB::commit();
    }

    /**
     * Rollback the active database transaction
     *
     * @return void
     */
    public function rollBack(): void
    {
        DB::rollBack();
    }

    /**
     * Get the number of active transactions
     *
     * @return int
     */
    public function transactionLevel(): int
    {
        return DB::transactionLevel();
    }
}
