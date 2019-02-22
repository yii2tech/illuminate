<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Http;

use Yii;
use Closure;
use Illuminate\Http\Request;
use yii\web\HttpException as YiiHttpException;
use yii\base\ExitException as YiiExitException;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * YiiApplicationMiddleware is a middleware, which processing Yii web application.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class YiiApplicationMiddleware
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application Laravel application instance.
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param  Application  $app Laravel application instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request, attempting to resolve it via Yii web application.
     *
     * @param  \Illuminate\Http\Request  $request request to be processed.
     * @param  \Closure  $next next pipeline request handler.
     * @param  string|null  $entryScript path to Yii application entry script relative to the project base path.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $entryScript = null)
    {
        $this->bootstrapYii();

        try {
            $this->runYii($entryScript);

            return new DummyResponse();
        } catch (YiiHttpException $e) {
            if ($e->statusCode == 404) {
                // If Yii indicates page does not exist - pass its resolving to Laravel
                return $next($request);
            }

            throw new HttpException($e->statusCode, $e->getMessage(), $e, [], $e->getCode());
        } catch (YiiExitException $e) {
            // In case Yii requests application termination - start one
            return new DummyResponse();
        }
    }

    /**
     * Makes preparations for Yii application run.
     */
    protected function bootstrapYii()
    {
        defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

        if (! class_exists('Yii')) {
            $this->app->make('path.base').'/vendor/yiisoft/yii2/Yii.php';
        }
    }

    /**
     * Runs Yii application from the given entry PHP script.
     *
     * @param  string|null  $entryScript path to Yii application entry script relative to the project base path.
     * @return mixed Yii entry script run result.
     */
    protected function runYii(?string $entryScript = null)
    {
        if ($entryScript === null) {
            $entryScript = 'legacy/web/index.php';
        }

        $entryScript = $this->app->make('path.base').DIRECTORY_SEPARATOR.$entryScript;

        return require $entryScript;
    }

    /**
     * Preforms clean up after running Yii application.
     */
    protected function terminateYii()
    {
        Yii::$classMap = [];
        Yii::$aliases = [];

        Yii::setLogger(null);
        Yii::$app = null;
        Yii::$container = null;
    }
}
