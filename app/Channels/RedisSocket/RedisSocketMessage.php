<?php

namespace App\Channels\RedisSocket;

use Illuminate\Support\Str;

class RedisSocketMessage
{

    public const CONV_QUEUE = ':cv';
    public const LST_QUEUE = ':lst';
    public const QUEUE_BONUS = ':bonus';
    public const CERT_QUEUE = ':cert';
    public const QUEUE_TYPING = ':typing';

    public const POST_QUEUE = ':posts';
    public const QUEUE_DEFAULT= ':default';

    public static $REDIS_QUEUES = [self::CONV_QUEUE, self::LST_QUEUE, self::QUEUE_BONUS, self::CERT_QUEUE, self::QUEUE_TYPING, self::POST_QUEUE, self::QUEUE_DEFAULT];


    public $command;
    public $queue;
    public $data;

    public function __construct( $command,  $data, $queue = self::QUEUE_DEFAULT)
    {
        $this->guid = Str::uuid();
        if ($queue){
            $this->queue = $queue;
        } else {
            $this->queue = self::QUEUE_DEFAULT;
        }

        $this->command = $command;
        $this->data = $data;
    }

    public function toJSON()
    {
        return json_encode([
            'command' => $this->command,
            'guid' =>   $this->guid,
            'redis_queue' => $this->queue,
            'data' => [
                'data' => $this->data
            ]
        ]);
    }
}
