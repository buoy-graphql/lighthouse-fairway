<?php

namespace Buoy\LighthouseFairway\Util;

use Buoy\LighthouseFairway\Schema\Enums\EventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Broadcast
{
    public static function modelEvent(Model $model, EventType $event, bool $shouldQueue = true): void
    {
        $subscription = Str::camel(class_basename($model)) . 'Modified';

        \Nuwave\Lighthouse\Execution\Utils\Subscription::broadcast($subscription, [
            'event' => $event,
            'id' => $model->id,
            'model' => $model,
        ], $shouldQueue);
    }
}
