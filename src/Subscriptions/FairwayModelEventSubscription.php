<?php

namespace Buoy\LighthouseFairway\Subscriptions;

use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;
use Nuwave\Lighthouse\Subscriptions\Subscriber;

abstract class FairwayModelEventSubscription extends GraphQLSubscription
{
    abstract public function filterSubscription(Subscriber $subscriber, $root): bool;

    public function filter(Subscriber $subscriber, $root): bool
    {
        // Filter based on event type
        if (optional($subscriber->args)['events']) {
            if (!in_array($root['event'], $subscriber->args['events'])) {
                return false;
            }
        }

        // Custom filtering
        return $this->filterSubscription($subscriber, $root);
    }
}
