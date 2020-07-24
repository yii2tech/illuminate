<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Http;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminatech\ArrayFactory\FactoryContract;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Yii;
use yii\web\HttpException as YiiHttpException;
use yii\base\ExitException as YiiExitException;

/**
 * YiiApplicationMiddleware is a middleware, which processing Yii web application.
 *
 * Kernel configuration example:
 *
 * ```php
 * namespace App\Http;
 *
 * use Illuminate\Foundation\Http\Kernel as HttpKernel;
 *
 * class Kernel extends HttpKernel
 * {
 *     protected $middleware = [
 *         \App\Http\Middleware\CheckForMaintenanceMode::class,
 *         \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
 *         \App\Http\Middleware\TrimStrings::class,
 *         \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
 *         // ...
 *         \Yii2tech\Illuminate\Http\YiiApplicationMiddleware::class,
 *     ];
 *     // ...
 * }
 * ```
 *
 * Route configuration example:
 *
 * ```php
 * Route::any('{fallbackPlaceholder}', function () {
 *     abort(404);
 * })
 *     ->middleware(Yii2tech\Illuminate\Http\YiiApplicationMiddleware::class)
 *     ->where('fallbackPlaceholder', '.*')
 *     ->fallback();
 * ```
 *
 * Each middleware instance is automatically configured from the configuration key 'yii.middleware' using [array factory](https://github.com/illuminatech/array-factory).
 *
 * @see \Illuminatech\ArrayFactory\FactoryContract
 * @see \Yii2tech\Illuminate\Yii\Web\Response
 * @see DummyResponse
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class YiiApplicationMiddleware
{
    /**
     * @var string default path to Yii application entry script relative to the project base path.
     * This value will be used only in case entry script is not specified as a middleware parameter.
     */
    public $defaultEntryScript = 'legacy/web/index.php';

    /**
     * @var string|null path to bootstrap file, which should be included before defining constants and including `Yii.php`.
     */
    public $bootstrap;

    /**
     * @var array|null array configuration for Yii DI container to be applied during Yii bootstrap.
     * If not set - container will not be explicitly setup.
     * @see FactoryContract::make()
     *
     * Example:
     *
     * ```php
     * [
     *     '__class' => Yii2tech\Illuminate\Yii\Di\Container::class,
     * ]
     * ```
     */
    public $container;

    /**
     * @var array|null array configuration for Yii logger to be applied during Yii bootstrap.
     * If not set - logger will not be explicitly setup.
     * @see FactoryContract::make()
     *
     * Example:
     *
     * ```php
     * [
     *     '__class' => Yii2tech\Illuminate\Yii\Log\Logger::class,
     * ]
     * ```
     */
    public $logger;

    /**
     * @var bool whether to perform cleanup of Yii application.
     */
    public $cleanup = true;

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

        $this->getFactory()->configure($this, $this->app->get('config')->get('yii.middleware', []));
    }

    /**
     * Returns related array factory for components creation and configuration.
     *
     * @return FactoryContract array factory instance.
     */
    public function getFactory(): FactoryContract
    {
        return $this->app->make(FactoryContract::class);
    }

    /**
     * Handle an incoming request, attempting to resolve it via Yii web application.
     *
     * @param  \Illuminate\Http\Request  $request request to be processed.
     * @param  \Closure  $next  next pipeline request handler.
     * @param  string|null  $entryScript  path to Yii application entry script relative to the project base path.
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ?string $entryScript = null)
    {
        $this->bootstrapYii();

        try {
            return $this->runYii($entryScript);
        } catch (YiiHttpException $e) {
            $this->cleanup();

            if ($e->statusCode == 404) {
                // If Yii indicates page does not exist - pass its resolving to Laravel
                return $next($request);
            }

            throw new HttpException($e->statusCode, $e->getMessage(), $e, [], $e->getCode());
        } catch (YiiExitException $e) {
            // In case Yii requests application termination - request is considered as handled
            return $this->createResponse();
        }
    }

    /**
     * Makes preparations for Yii application run.
     */
    protected function bootstrapYii()
    {
        if ($this->bootstrap) {
            require $this->bootstrap;
        }

        defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', false);

        defined('YII_DEBUG') or define('YII_DEBUG', $this->app->get('config')->get('app.debug', false));

        if (! defined('YII_ENV')) {
            $environment = $this->app->get('config')->get('app.env', 'production');
            switch ($environment) {
                case 'production':
                    $environment = 'prod';
                    break;
                case 'local':
                case 'development':
                    $environment = 'dev';
                    break;
                case 'testing':
                    $environment = 'test';
                    break;
            }

            define('YII_ENV', $environment);
        }

        if (! class_exists('Yii')) {
            require $this->app->make('path.base').'/vendor/yiisoft/yii2/Yii.php';
        }

        if ($this->container) {
            Yii::$container = $this->getFactory()->make($this->container);
        }

        if ($this->logger) {
            Yii::setLogger($this->getFactory()->make($this->logger));
        }
    }

    /**
     * Runs Yii application from the given entry PHP script.
     *
     * @param  string|null  $entryScript path to Yii application entry script relative to the project base path.
     * @return \Illuminate\Http\Response HTTP response instance.
     */
    protected function runYii(?string $entryScript = null): Response
    {
        if ($entryScript === null) {
            $entryScript = $this->defaultEntryScript;
        }

        $entryScript = $this->app->make('path.base').DIRECTORY_SEPARATOR.$entryScript;

        require $entryScript;

        return $this->createResponse();
    }

    /**
     * Performs clean up after running Yii application in case {@see $cleanup} is enabled.
     */
    protected function cleanup()
    {
        if ($this->cleanup) {
            $this->terminateYii();
        }
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

    /**
     * Creates HTTP response for this middleware.
     * In case Yii application uses response, which allows its conversion into Laravel one, such conversion will be perfromed.
     * Otherwise a dummy response will be generated.
     * This method performs automatic clean up.
     *
     * @see \Yii2tech\Illuminate\Yii\Web\Response
     * @see DummyResponse
     *
     * @return \Illuminate\Http\Response HTTP response instance.
     */
    protected function createResponse(): Response
    {
        if (headers_sent()) {
            $this->cleanup();

            return new DummyResponse();
        }

        $yiiResponse = Yii::$app ? Yii::$app->get('response') : null;

        $this->cleanup();

        if ($yiiResponse instanceof \Yii2tech\Illuminate\Yii\Web\Response) {
            return $yiiResponse->getIlluminateResponse(true);
        }

        return new DummyResponse();
    }
}
