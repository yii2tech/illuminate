<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Http;

use Closure;
use Illuminate\Http\Request;

/**
 * YiiApplicationMiddleware is a middleware, which processing Yii web application.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class YiiApplicationMiddleware
{
    /**
     * Handle an incoming request, attempting to resolve it via Yii web application.
     *
     * @param  \Illuminate\Http\Request  $request request to be processed.
     * @param  \Closure  $next next pipeline request handler.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
