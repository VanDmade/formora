<?php

namespace VanDmade\EntryEase;

use Illuminate\Support\ServiceProvider;

class EntryEaseServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/components/EntryEase'),
            __DIR__.'/../resources/sass' => resource_path('sass'),
        ]);
    }

}