<?php

namespace Buoy\LighthouseFairway\Util;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Broadcast
{
    public static function modelEvent(Model $model, string $event, bool $shouldQueue = true): void
    {
        $subscription = Str::camel(class_basename($model)) . 'Modified';

        \Nuwave\Lighthouse\Execution\Utils\Subscription::broadcast($subscription, [
            'event' => $event,
            'id' => $model->id,
            'model' => $model,
        ], $shouldQueue);
    }
}
