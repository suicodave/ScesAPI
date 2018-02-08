<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Student;
use App\Election;
use App\Notifications\ElectionStarted;

class ProcessElectionStartedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $students;
    protected $election;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($students, Election $election)
    {
        $this->students = $students;
        $this->election = $election;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $students = $this->students;
        $election = $this->election;
        foreach ($students as $student) {

            $student->notify(new ElectionStarted($election));
        }
    }
}
