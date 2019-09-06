<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Log;

use yii\base\Component;
use yii\helpers\VarDumper;

/**
 * Logger sends log messages to Laravel logger.
 *
 * Logger can be setup at application entry script:
 *
 * ```php
 * <?php
 *
 * Yii::setLogger(new Yii2tech\Illuminate\Yii\Log\Logger);
 *
 * $config = require(__DIR__ . '/../config/web.php');
 *
 * (new yii\web\Application($config))->run();
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Logger extends \yii\log\Logger
{
    use Illuminated;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        Component::init(); // skip parent init, avoiding `register_shutdown_function()` call.
    }

    /**
     * {@inheritdoc}
     */
    public function log($message, $level, $category = 'application'): void
    {
        $level = $this->convertLogLevel($level);
        $context = [
            'category' => $category,
        ];
        if (! is_string($message)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($message instanceof \Throwable) {
                $context['exception'] = $message;
                $message = (string) $message;
            } else {
                $message = VarDumper::export($message);
            }
        }

        $this->getIlluminateLogger()->log($level, $message, $context);
    }
}
