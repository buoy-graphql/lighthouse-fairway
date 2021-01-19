# Lighthouse Fairway

This extension brings [Fairway](https://github.com/buoy-graphql/fairway-spec)-compatibility to
[Lighthouse](https://lighthouse-php.com), which makes Lighthouse fully compatible with the [Buoy-client](https://ngx-buoy.com).

## Installation

```shell
composer require buoy/lighthouse-fairway
```

## Configuration

Lighthouse Fairway has a configuration file that can be published:

```shell
php artisan vendor:publish --tag=lighthouse-fairway-config  
```

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

### Dispatching events

Events are dispatched with the Broadcast-utility.
Simply supply the model and event type, and the event will be broadcast to all authorized subscribers.

```php
$note = App\Models\Note::first();

Buoy\LighthouseFairway\Util\Broadcast::modelEvent($note, 'update');
```