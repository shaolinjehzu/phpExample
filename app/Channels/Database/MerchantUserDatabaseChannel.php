<?php

namespace App\Channels\Database;

use App\Models\Merchant\Merchant;
use App\Models\Merchant\Program;
use Exception;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notification;
use App\Models\Merchant\MerchantsNotifications;

class MerchantUserDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {

        $users = $notifiable->getEmployees();

        try {

            DB::beginTransaction();
            foreach ($users as $user) {

                MerchantsNotifications::Create($this->buildPayload($notifiable, $notification, $user->id));

            }

            $notif = MerchantsNotifications::latest()->first();
            broadcast(new \App\Events\NewMerchantNotificationEvent($notifiable->id, $notif));

                DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            throw $ex;
        }

        /* return $notifiable->routeNotificationFor('merchantDatabase', $notification)->create(
            $this->buildPayload($notifiable, $notification)
        ); */
    }

    /**
     * Get the data for the notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getData($notifiable, $notification)
    {

        if (method_exists($notification, 'toMerchantDatabase')) {
            return is_array($data = $notification->toMerchantDatabase($notifiable))
                ? $data : $data->data;
        } else {

            throw new RuntimeException(json_encode($notification));
        }
    }

    /**
     * Build an array payload for the DatabaseNotification Model.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array
     */
    protected function buildPayload($notifiable, $notification, $user_id)
    {
        $programId =  $notification->programId ?? null;
        return  [
            'id' => Str::uuid(),
            'type' => get_class($notification),
            'merchant_id' => $notifiable->id,
            'user_id' => $user_id,
            'data' => $notification->toMerchantDatabase($notifiable),
            'read_at' => null,
            'program_id' => $programId
        ];
    }
}
