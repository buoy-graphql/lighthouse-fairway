<?php

namespace Buoy\LighthouseFairway\Schema\Enums;

enum EventType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
