<?php

declare(strict_types=1);

namespace App\Application\Jobs;

use App\Application\Queue\JobInterface;

class LogMessageJob implements JobInterface
{
    public function __construct(
        private string $message,
        private string $level = 'info'
    ) {}

    public function handle(): void
    {
        // For testing, just echo to stdout which will be captured by the worker output
        // In a real scenario, this would write to a log file or DB.
        
        // Simulating some work
        usleep(100000); // 100ms
        
        $output = sprintf(
            "[LogMessageJob] Level: %s | Message: %s | Time: %s",
            strtoupper($this->level),
            $this->message,
            date('H:i:s')
        );
        
        echo $output . PHP_EOL; // This will show up in the worker's stdout
    }
}
