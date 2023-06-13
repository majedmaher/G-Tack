<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderTracking implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order , $data , $channelName , $socketId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($order , $data , $channelName , $socketId)
    {
        $this->order = $order;
        $this->data = $data;
        $this->channelName = $channelName;
        $this->socketId = $socketId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel($this->channelName);
    }

    public function broadcastAs()
    {
        return 'new-vendor-location';
    }

    /**
     * Get the additional data to broadcast with the event.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'channel_name' => $this->channelName,
            'socket_id' => $this->socketId,
        ];
    }
}
