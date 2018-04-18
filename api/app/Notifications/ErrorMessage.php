<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class ErrorMessage extends Notification
{
    use Queueable;
    private $message;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($message){
        //
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable){
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable){
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable){
        return [
            //
        ];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable){
        return (new SlackMessage)->
            content(env('APP_NAME'))->
            attachment(function ($attachment){
                $attachment->
                    title(env('APP_NAME'))->fields([
                        'Message' => $this->message['message'],
                        'File' => $this->message['file'],
                        'URL' => $this->message['url'],
                        'Input' => $this->message['input']
                    ]);
            });
            # content(print_r($this->message, true));
    }
}
