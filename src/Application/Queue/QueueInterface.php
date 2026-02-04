<?php

declare(strict_types=1);

namespace App\Application\Queue;

interface QueueInterface
{
    /**
     * Push a new job onto the queue.
     *
     * @param string $jobClass The fully qualified class name of the job (must implement JobInterface).
     * @param array  $data     Data to be passed to the job constructor.
     * @param string $queue    The queue name (default: 'default').
     * @return int The ID of the inserted job.
     */
    public function push(string $jobClass, array $data = [], string $queue = 'default'): int;
}
