<?php

namespace App\Channels\RedisSocket;

use App\Models\User\UserDevice;
use App\Models\Merchant\Program;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Carbon\Carbon;
use App\Facades\Auth;

class RedisSocketChannel
{

    /**
     * Send the given notification.
     *
     * @param  mixed $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     */

    public function send($notifiable, Notification $notification)
    {

        if ($notifiable instanceof User) {
            Auth::loginUsingId($notifiable->getKey()); //Отправка от имени получателя

            $message = $notification->toRedisSocketMessage($notifiable);
            $current = now();
            Log::channel('redis')->debug(__METHOD__, [
                'command' => $message->command,
                'guid' =>   $message->guid,
                'redis_queue' => $message->queue,
                'notifiable_pk' => $notifiable->getKey()
            ]);

            $user_devices = $notifiable->AllChatDevices;

            foreach ($user_devices as $user_device) {

                $lastActivity = new Carbon($user_device->last_activity);

                $deltaTime = $lastActivity->diffInSeconds($current, false);

                if ($deltaTime < config('database.redis.timeout', 3600)) {
                    $redis_channel = $user_device->getChannelName() . $message->queue;
                    Log::channel('redis')->debug("Redis::rPush($redis_channel)", [
                        'guid' => $message->guid,
                    ]);
                    Redis::rPush($redis_channel, $message->toJSON());

                    Redis::expire($redis_channel, config('database.redis.timeout', 3600));
                    Redis::publish($redis_channel, 'publish');
                    Log::channel('redis')->debug("Redis::publish($redis_channel)", );
                }
            }
        }
    }
}
