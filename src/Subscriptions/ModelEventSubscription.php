<?php

namespace Buoy\LighthouseFairway\Subscriptions;

use Illuminate\Http\Request;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;
use Nuwave\Lighthouse\Subscriptions\Subscriber;

class ModelEventSubscription extends GraphQLSubscription
{
    public function authorize(Subscriber $subscriber, Request $request)
    {
        return true;
    }

    public function filter(Subscriber $subscriber, $root)
    {
        return true; // TODO
    }
}