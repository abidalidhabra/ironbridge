<?php

namespace App\Providers;

use App\Refacing\Contracts\EventRefaceInterface;
use App\Refacing\Contracts\EventsMiniGameRefaceInterface;
use App\Refacing\EventReface;
use App\Refacing\EventsMiniGameReface;
use App\Repositories\Contracts\EventInterface;
use App\Repositories\Contracts\EventsMiniGameInterface;
use App\Repositories\Contracts\EventsUserInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\EventRepository;
use App\Repositories\EventsMiniGameRepository;
use App\Repositories\EventsUserRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class IronBridgeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(EventInterface::class, EventRepository::class);
        $this->app->bind(EventsUserInterface::class, EventsUserRepository::class);
        $this->app->bind(EventsMiniGameInterface::class, EventsMiniGameRepository::class);
        $this->app->bind(EventsMiniGameRefaceInterface::class, EventsMiniGameReface::class);
        $this->app->bind(EventRefaceInterface::class, EventReface::class);
        app()->bind(UserInterface::class, function () {
            return function ($user) {
                return new UserRepository($user);
            };
        });
    }
}
