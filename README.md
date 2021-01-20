# Lighthouse Fairway

This extension brings [Fairway](https://github.com/buoy-graphql/fairway-spec)-compatibility to
[Lighthouse](https://lighthouse-php.com), which makes it fully compatible with the [Buoy-client](https://ngx-buoy.com).

## Installation

```shell
composer require buoy/lighthouse-fairway
```

Then publish the configuration.

```shell
php artisan vendor:publish --tag=lighthouse-fairway-config  
```

Then create the class that will handle authorization and filtering for your subscriptions:

```php
<?php

namespace App\GraphQL\Subscriptions;

use Buoy\LighthouseFairway\Subscriptions\FairwayModelEventSubscription;
use Illuminate\Http\Request;
use Nuwave\Lighthouse\Subscriptions\Subscriber;

class FairwayModelSubscription extends FairwayModelEventSubscription
{
    public function authorize(Subscriber $subscriber, Request $request): bool
    {
        // Authorize the user
        return true;
    }

    public function filterSubscription(Subscriber $subscriber, $root): bool
    {
        // Add filtering here. Filtering based on event type is handled for you.
        return true;
    }
}
```

Lastly, enter the namespace for your subscription-class in the `lighthouse-fairway.php` config-file


## Usage

This library adds some shortcuts to making models subscribable.


### Subscribable-directive
The directive is applied to the model type. In this example, we assume the model `App\Models\Note` exists.

```graphql
type Note @subscribable {
    id
    text
}
```

Applying the `@subscribable` directive will automatically add following to your schema:
```graphql
enum EventType {
    CREATE
    UPDATE
    DELETE
}

type NoteEvent {
    "ID of the model"
    id: ID!
    "Type of the event"
    event: EventType!
    "The model that has been modified"
    model: Note!
}

type Subscription {
    noteModified(
        "Limit the subscription to a specific model"
        id: ID,
        "Limit the subscription to specific events"
        events: [EventType!]
    ): NoteEvent
}
```

The @subscribable directive can also use a custom subscription class if needed:
```graphql
type Note @subscribable(class: "\\\\App\\\\GraphQL\\\\Subscriptions\\\\MyCustomSubscription") {
    id
    text
}
```
Just make sure that it returns data that conforms to the generated schema.
It is recommended to extend `Buoy\LighthouseFairway\Subscriptions\FairwayModelEventSubscription` in order to maintain the event-type filtering.

### Dispatching events

Events are dispatched with the Broadcast-utility.
Simply supply the model and event type, and the event will be broadcast to all authorized subscribers.

```php
$note = App\Models\Note::first();
Buoy\LighthouseFairway\Util\Broadcast::modelEvent($note, 'update');
```
