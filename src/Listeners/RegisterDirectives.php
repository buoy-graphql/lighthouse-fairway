<?php

namespace Buoy\LighthouseFairway\Listeners;

class RegisterDirectives
{
    public function handle()
    {
        return [
            'Buoy\\LighthouseFairway\\Schema\\Directives',
        ];
    }
}
