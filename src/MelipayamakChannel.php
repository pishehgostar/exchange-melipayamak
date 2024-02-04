<?php

namespace Pishehgostar\ExchangeMelipayamak;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;


class MelipayamakChannel
{
    /**
     * Channel constructor.
     */
    public function __construct(private Dispatcher $dispatcher)
    {

    }


    /**
     * @throws \Exception
     */
    public function send(mixed $notifiable, Notification $notification):bool|string
    {
        $message = $notification->toMelipayamak($notifiable);

        if ($message->toNotGiven()) {
            $to = $notifiable->routeNotificationFor('melipayamak', $notification)
                ?? $notifiable->routeNotificationFor(self::class, $notification);
            if (! $to) {
                return false;
            }

            $message->to($to);
        }

        try {
            $body = $message->send();
            $response = json_decode($body,true);
            if (isset($response['RetStatus']) && $response['RetStatus']===1){
                return $response['Value']??false;
            }else{
                $exception = new \Exception("Melipayamak: Failed send sms.");
                $this->dispatcher->dispatch(new NotificationFailed($notifiable, $notification, 'melipayamak', [
                    'to' => $to,
                    'request' => $message->toArray(),
                    'exception' => $exception,
                ]));

                throw $exception;
            }
        } catch (\Exception $exception) {
            $this->dispatcher->dispatch(new NotificationFailed($notifiable, $notification, 'melipayamak', [
                'to' => $to,
                'request' => $message->toArray(),
                'exception' => $exception,
            ]));

            throw $exception;
        }
    }
}
