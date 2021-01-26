<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $PUSH_NOTIFICATION_RECEIVERS;
    public $PUSH_NOTIFICATION;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($current_receiver_id, $notification_id)
    {
        $this->PUSH_NOTIFICATION_RECEIVERS = $current_receiver_id;
        $this->PUSH_NOTIFICATION = $notification_id;
    }

    public function broadcastWith()
    {
        return [
            'PUSH_NOTIFICATION_RECEIVERS' => $this->PUSH_NOTIFICATION_RECEIVERS,
            'PUSH_NOTIFICATION'=> $this->PUSH_NOTIFICATION
        ];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('fuoye360_channel');
    }
}
