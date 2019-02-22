<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate;

use Carbon\Laravel\ServiceProvider;
use Yii2tech\Illuminate\Console\RenameNamespaceCommand;

/**
 * YiiIlluminateServiceProvider
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class YiiIlluminateServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->bootCommands();
        }
    }

    protected function bootCommands()
    {
        $this->commands([
            RenameNamespaceCommand::class,
        ]);
    }
}
