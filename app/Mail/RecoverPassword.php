<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;
use App\User;
use Config;
use Log;

class RecoverPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $recoverLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $domain = Config::get('app.frontend_url');

        $this->user = $user;
        $this->recoverLink = "$domain/#!/public/auth/recovery/$user->recovery_token";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Forgot your password?')->view('emails.auth.recover');
    }
}
