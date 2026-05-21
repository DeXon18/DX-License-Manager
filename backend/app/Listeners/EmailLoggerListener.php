<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Illuminate\Support\Facades\Log;

class EmailLoggerListener
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;
        
        if (!$message instanceof Email) {
            return;
        }

        $recipients = array_map(function (Address $address) {
            return $address->getAddress();
        }, $message->getTo());

        if (\Illuminate\Support\Facades\Schema::hasTable('email_logs')) {
            DB::table('email_logs')->insert([
                'recipient' => implode(', ', $recipients),
                'subject' => $message->getSubject(),
                'mailable_class' => $event->data['mailable'] ?? null,
                'status' => 'sent',
                'created_at' => now(),
            ]);
        }
    }
}
