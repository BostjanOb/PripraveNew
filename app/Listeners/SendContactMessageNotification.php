<?php

namespace App\Listeners;

use App\Events\ContactMessageSubmitted;
use App\Mail\ContactMessageSubmittedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendContactMessageNotification implements ShouldQueue
{
    public function handle(ContactMessageSubmitted $event): void
    {
        Mail::to(config('services.contact.to'))
            ->send(new ContactMessageSubmittedMail($event->contactMessage));
    }
}
