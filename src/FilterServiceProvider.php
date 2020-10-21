<?php

namespace Iq500\NovaSearchableBelongsToFilter;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FilterServiceProvider extends ServiceProvider
{
    protected static $availableTranslations = [
        'ru'
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-searchable-belongs-to-filter', __DIR__.'/../dist/js/filter.js');
            Nova::style('nova-searchable-belongs-to-filter', __DIR__.'/../dist/css/filter.css');

            $translations = \Iq500\NovaSearchableBelongsToFilter\FilterServiceProvider::getTranslationsFromAppLocale();
            if(!is_null($translations))
                Nova::translations($translations);
        });
    }

    public static function getTranslationsFromAppLocale()
    {
        $locale = App::getLocale();

        if(in_array($locale, static::$availableTranslations))
            return __DIR__ . "/../resources/lang/$locale.json";

        return null;
    }
}
