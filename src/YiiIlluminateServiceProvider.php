<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate;

use Illuminate\Support\ServiceProvider;
use Yii2tech\Illuminate\Console\RenameNamespaceCommand;

/**
 * YiiIlluminateServiceProvider bootstraps tools supporting application migration from Yii to Laravel.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class YiiIlluminateServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $defer = false;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerPublications();
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->bootCommands();
        }
    }

    /**
     * Register resources to be published by the publish command.
     */
    protected function registerPublications(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        /*$this->publishes([
            __DIR__ . '/../config/yii.php' => $this->app->make('path.config').DIRECTORY_SEPARATOR.'yii.php',
        ], 'config');*/

        if (! class_exists(\InitialMigration::class)) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/../database/migrations/initial_migration.php.stub.php' => $this->app->databasePath().'/migrations/'.$timestamp.'_initial_migration.php',
            ], 'migrations');
        }
    }

    /**
     * Boots provided console commands.
     */
    protected function bootCommands(): void
    {
        $this->commands([
            RenameNamespaceCommand::class,
        ]);
    }
}
