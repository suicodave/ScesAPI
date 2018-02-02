<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Cloudinary\Uploader;
use App\Candidate;

class UploadImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $file;
    private $candidate;
    public function __construct(string $file, Candidate $candidate)
    {
        $this->file = $file;
        $this->candidate = $candidate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $candidate = $this->candidate;
        $upload = Uploader::upload($this->file, array('folder' => 'candidate_image', 'timeout' => 300));
        $candidate->profile_image = $upload['secure_url'];
        $candidate->save();
    }
}
