<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonMail extends Mailable{
    use Queueable, SerializesModels;

    public $template;
    public $params;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template, $subject, $params){
        $this->template = $template;
        $this->params = $params;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        try{
            return $this->
                from(config('app.from_email'))->
                subject($this->subject)->
                view($this->template)->
                with($this->params);
        }catch(\Exception $e){
            var_dump($e);die;
        }catch(\Error $e){
            var_dump($e);die;
        }
    }
}
