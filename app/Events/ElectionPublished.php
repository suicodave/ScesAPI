<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Http\Resources\Election as ElectionResource;

class ElectionPublished implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $election;
    private $rawElection;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($election)
    {
        $this->election = new ElectionResource($election);
        $this->rawElection = $election;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = array();
        $election = $this->rawElection;
        $departments = explode(' ', $election->department_ids);
        foreach ($departments as $department) {
            $channel = 'election' . $department . 'sy' . $election->school_year_id;
            array_push($channels, $channel);
        }
        return $channels;
    }
}
