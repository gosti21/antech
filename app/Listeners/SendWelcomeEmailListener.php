<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Número de intentos del listener
     */
    public $tries = 3;

    public $queue = 'emails';
    
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        // El listener despacha el Job
        SendWelcomeEmail::dispatch($event->user);
    }

    /**
     * Manejar el fallo del listener
     */
    public function failed(UserRegistered $event, \Throwable $exception): void
    {
        Log::error('Error en el listener de correo de bienvenida', [
            'user_id' => $event->user->id,
            'error' => $exception->getMessage()
        ]);
    }
}
