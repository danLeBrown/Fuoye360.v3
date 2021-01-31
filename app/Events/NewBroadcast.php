<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $PUSH_NOTIFICATION_RECEIVERS;
    public $PUSH_BROADCASTS;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($PUSH_NOTIFICATION_RECEIVERS, $PUSH_BROADCASTS)
    {
        $this->PUSH_NOTIFICATION_RECEIVERS = $PUSH_NOTIFICATION_RECEIVERS;
        $this->PUSH_BROADCASTS = $PUSH_BROADCASTS;
    }

    public function broadcastWith()
    {
        return [
            'PUSH_NOTIFICATION_RECEIVERS'=> $this->PUSH_NOTIFICATION_RECEIVERS,
            'PUSH_BROADCASTS' => $this->PUSH_BROADCASTS
        ];
    }

    public function broadcastAs()
    {
        return 'NewBroadcast';
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
