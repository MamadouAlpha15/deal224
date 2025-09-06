<?php

namespace App\Events;

use App\Models\BoostMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(BoostMessage $message)
    {
        $this->message = $message;
    }

    // Canal privé pour chaque paiement (chat isolé)
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->boost_payment_id);
    }

    public function broadcastAs()
    {
        return 'new-message';
    }
}
