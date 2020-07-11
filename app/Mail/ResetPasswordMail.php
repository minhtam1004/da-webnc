<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public $OTPCode;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($OTPCode)
    {
        $this->OTPCode = $OTPCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail Reset Password from KLXBanking')->view('emails.OTPMail');
    }
}
