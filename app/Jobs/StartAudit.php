<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StartAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $auditor;

    /**
     * Create a new job instance.
     */
    public function __construct($auditor)
    {
        $this->auditor = $auditor;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contents = $this->auditor->audit();
        dump("Done.");
    }
}
