<?php

namespace Buoy\LighthouseFairway\Util;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Broadcast
{
    public static function modelEvent(Model $model, string $event, bool $queue): void
    {
        $event = Str::camel(class_basename($model)) . 'Modified';

        \Nuwave\Lighthouse\Execution\Utils\Subscription::broadcast($event, [
            'event' => $event,
            'id' => $model->id,
            'model' => $model,
        ]);
    }
}
