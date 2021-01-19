<?php

namespace Buoy\LighthouseFairway\Schema\Enums;

use BenSampo\Enum\Enum;

class EventType extends Enum
{
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
}
