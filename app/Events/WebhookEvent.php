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

    public $pdf_links;

    public $prototype_links;

    public $category;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($category, $prototype_links, $pdf_links)
    {
        $this->category        = $category;
        $this->pdf_links       = $pdf_links;
        $this->prototype_links = $prototype_links;
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
