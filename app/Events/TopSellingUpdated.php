<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;

class TopSellingUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $name;  // top-selling product name
    public $id;    // optional, product id

    public function __construct($name, $id)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('products'); // public channel
    }

    public function broadcastAs(): string
    {
        return 'top-selling-updated';
    }
}
