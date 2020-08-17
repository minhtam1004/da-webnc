<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTPMail extends Mailable
{
    use Queueable, SerializesModels;
    public $OTPCode;
    public $accountNumber;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($OTPCode,$accountNumber,$name)
    {
        $this->OTPCode = $OTPCode;
        $this->accountNumber = $accountNumber;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Mail confirm OTP from KLXBanking')->view('emails.OTPMail');
    }
}
