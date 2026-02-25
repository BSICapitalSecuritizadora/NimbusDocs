<?php

declare(strict_types=1);

namespace App\Application\Queue;

/**
 * Interface that all generic background jobs must implement.
 */
interface JobInterface
{
    /**
     * Execute the job logic.
     *
     * @return void
     */
    public function handle(): void;
}
