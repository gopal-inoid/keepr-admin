<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestEmailSender extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $subject;
    public $mailbody;
    public function __construct($subject,$mailbody)
    {
        $this->subject=$subject;
        $this->mailbody=$mailbody;
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject=$this->subject;
        $mailbody=$this->mailbody;
        //return $this->view('email-templates.mail-tester');
        return $this->view('email-templates.mail-tester')->subject($subject)->with('mailbody',$mailbody);
    }
}
