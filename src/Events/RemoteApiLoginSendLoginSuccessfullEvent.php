<?php

namespace Wergh\RemoteApiLogin\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Wergh\RemoteApiLogin\Entities\RemoteApiLogin;

class RemoteApiLoginSendLoginSuccessfullEvent implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public RemoteApiLogin $apiLoginInstance;

    /**
     * Create a new event instance.
     */
    public function __construct($authenticableInstance, string $code)
    {
        $this->apiLoginInstance = RemoteApiLogin::searchByCode($code);
        $this->apiLoginInstance->authenticatable()->associate($authenticableInstance);
        $this->apiLoginInstance->save();

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel>
     */

    public function broadcastOn(): Channel
    {
        return new Channel(Config::get('remote-api-login.channel_socket_name').$this->apiLoginInstance->uuid);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return Config::get('remote-api-login.broadcast_event');
    }

    public function broadcastWith(): array
    {
        return [

        ];
    }

}
