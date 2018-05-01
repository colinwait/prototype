<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WebhookEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $file_links;

    public $category;

    public $type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($category, $type, $file_links)
    {
        $this->category   = $category;
        $this->type       = $type;
        $this->file_links = $file_links;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
