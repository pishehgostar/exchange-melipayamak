<?php

namespace Pishehgostar\ExchangeMelipayamak;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Melipayamak\MelipayamakApi;

class MainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Notification::resolved(static function (ChannelManager $service) {
            $service->extend('melipayamak', static fn($app) => $app->make(MelipayamakChannel::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
//
    }
}
