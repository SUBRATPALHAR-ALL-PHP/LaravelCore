<?php

namespace App\Listeners;

use App\Events\somethingHappenedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Mail;
use App\Mail\SubratTest;


class SendEmailListeners
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  somethingHappenedEvent  $event
     * @return void
     */
    public function handle(somethingHappenedEvent $event)
    {
        // $event->whatHappened ?
        // if(true) {$this->release(30);}
        Mail::to(env('MY_EMAIL'))->send(new SubratTest());
    }
}
