<?php

namespace devrkb21\PathaoLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PathaoWebhookReceived
{
    use Dispatchable, SerializesModels;

    /**
     * The raw webhook payload
     *
     * @var array
     */
    public $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
