<?php

namespace App\Channels\SMS;

use Illuminate\Notifications\Notification;
use App\Services\SMSRU;
use Illuminate\Support\Facades\Log;


class SMSChannel
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

        if (! $toList = $notifiable->routeNotificationFor('sms')) {
            return;
        }
        $message = $notification->toSMS($notifiable);
        /*
         * SMS.RU
         */
        $api_key = config('smsru.key');
        $smsru = new SMSRU($api_key); // Ваш уникальный программный ключ, который можно получить на главной странице

        //$data = new stdClass();
        if (!is_array($toList)){
            $toList = [$toList];
        }

        foreach($toList as $to){
            $data = (object)[
                'to' => $to,
                'text' => $message->message,
            ];
            //$data->to = $message->to;
            //$data->text = $message->message; // Текст сообщения
            // $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
            // $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
            // $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
            // $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
            // $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
            $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

            if ($sms && $sms->status == "ERROR"){
                Log::channel('smsru')->error(['data'=>$data, 'result'=>$sms]);
            } else {
                Log::channel('smsru')->debug(['data'=>$data, 'result'=>$sms]);
            }
        }




    }

}
