<?php
namespace MyForksFiles\FixThis;

use Illuminate\Support\ServiceProvider;

/**
 * This is the service provider.
 *
 * Place the line below in the providers array inside app/config/app.php
 * <code>'MyForksFiles\FixThis\FixThisServiceProvider',</code>
 *
 * @package FixThis
 * @author MyForksFiles
 *
 **/
class FixThisServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * @var array
     */
    protected $commands = [
        'MyForksFiles\FixThis\FixThisCommand',
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $langPath = __DIR__ . '/../resources/lang';
        $this->loadTranslationsFrom($langPath, 'fixthis');

    }

    /**
     * Register the command.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        $this->app->singleton(FixThisFacade::class, function () {
            return new FixThisFacade();
        });
        $this->app->alias(FixThisFacade::class, 'fixthis');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [__CLASS__];
    }

}