<?php

namespace Buoy\LighthouseFairway;

use Buoy\LighthouseFairway\Listeners\RegisterDirectives;
use Buoy\LighthouseFairway\Schema\Enums\EventType;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Events\RegisterDirectiveNamespaces;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;

class FairwayServiceProvider extends ServiceProvider
{
    protected $listen = [
        RegisterDirectiveNamespaces::class => [
            RegisterDirectives::class,
        ],
    ];

    public function boot(TypeRegistry $typeRegistry, Dispatcher $dispatcher): void
    {
        $this->publishes([
            __DIR__.'/lighthouse-fairway.php' => $this->app->configPath().'/lighthouse-fairway.php',
        ], 'lighthouse-fairway-config');

        // Register types
        $typeRegistry->register(new LaravelEnumType(EventType::class));

        // Register directives
        if (config('lighthouse-fairway.register_directives')) {
            $dispatcher->listen(
                RegisterDirectiveNamespaces::class,
                RegisterDirectives::class
            );
        }

        // Register client directives
        if (config('lighthouse-fairway.add_client_directives')) {
            // TODO
        }
    }

    public function register(): void
    {

    }
}
