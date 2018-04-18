<?php
namespace App\Helpers;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class SlackNotification extends Model{
    // Import Notifiable Trait
    use Notifiable;
    // Specify Slack Webhook URL to route notifications to
    public function routeNotificationForSlack() {
        return config('app.SLACK_WEBHOOK_URL');
    }
}