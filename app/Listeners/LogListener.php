<?php

namespace App\Listeners;

use App\Events\LogEvent;
use App\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogListener
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
     * @param  object $event
     *
     * @return void
     */
    public function handle(LogEvent $event)
    {
        $log = [
            'type'     => $event->type,
            'content'  => $event->content,
            'user_id'  => auth()->user() ? auth()->user()->id : 0,
            'username' => auth()->user() ? auth()->user()->name : '',
            'ip'       => request()->getClientIp()
        ];
        Log::create($log);
    }
}
