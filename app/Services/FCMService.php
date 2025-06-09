<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging;

class FCMService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendFCMV1($fcmToken, $title, $body)
    {
        $message = CloudMessage::withTarget('token', $fcmToken)
            ->withNotification(Notification::create($title, $body))
            ->withData([
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]);

        try {
            $this->messaging->send($message);
            return true; // sukses
        } catch (\Throwable $e) {
            // log error, jangan return response di sini, serahkan ke controller
            \Log::error('FCM send error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendNotification(string $token, string $title, string $body)
    {
        $notification = Notification::create($title, $body);
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);

        return $this->messaging->send($message);
    }
}
