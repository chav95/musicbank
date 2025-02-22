<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;
    
    protected $content;

    /*public $data;
    public $title;
    public $body;*/
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;   
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        /*return $this
            ->from('chavin.pradana@mncgroup.com')
            ->subject('Test Sending Mail')
            ->view('mail.mail_template')
            ->with('data', $this->data);*/

        return $this->markdown('mail.sendmail');
    }
}
