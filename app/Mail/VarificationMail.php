<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VarificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $Details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Details)
    {
        $this->Details = $Details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("this is subject")->view('MailVarification');
    }
}
