<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class Vote implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $meta;
    private $election;
    private $standing;
    private $standing_masked;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($meta)
    {
        $this->meta = $meta;
        $this->election = $meta['election'];
        $this->standing_masked = $meta['standing_masked'];
        $this->standing = $meta['standing'];

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = array();
        $election = $this->election;
        $departments = explode(' ', $election->department_ids);
        foreach ($departments as $department) {
            $channel = 'vote' . $department . 'sy' . $election->school_year_id;
            array_push($channels, $channel);
        }
        return $channels;
    }
}
