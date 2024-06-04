<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    public function userCodeSend(int $code, string $email)
    {
        Mail::send('mails.code', ['code' => $code], function ($message) use($email) {
            $message->to($email);
            $message->subject('Код для входа в аккаунт');
        });
    }
}
