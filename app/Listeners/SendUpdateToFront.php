<?php

namespace App\Listeners;

use App\Events\PaymentStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUpdateToFront
{
    /**
     * Create the event listener.
     */
    public function __construct(public $paymentUpdate)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentStatusUpdated $event)
    {
        return $event;
    }

    public function broadcastAs()
    {
        return 'PaymentStatusUpdated';
    }
}
