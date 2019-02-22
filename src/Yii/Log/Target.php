<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Log;

use yii\log\Logger;
use Psr\Log\LogLevel;
use yii\helpers\VarDumper;
use Illuminate\Log\Logger as LaravelLogger;

/**
 * Target passes log entries to Laravel logger.
 *
 * @property \Illuminate\Log\Logger $laravelLogger related Laravel logger instance.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Target extends \yii\log\Target
{
    /**
     * @var \Illuminate\Log\Logger laravel logger instance.
     */
    private $_laravelLogger;

    /**
     * @return \Illuminate\Log\Logger
     */
    public function getLaravelLogger(): LaravelLogger
    {
        if ($this->_laravelLogger === null) {
            $this->_laravelLogger = $this->defaultLaravelLogger();
        }

        return $this->_laravelLogger;
    }

    /**
     * @param  \Illuminate\Log\Logger  $laravelLogger
     * @return static self reference.
     */
    public function setLaravelLogger(LaravelLogger $laravelLogger): self
    {
        $this->_laravelLogger = $laravelLogger;

        return $this;
    }

    /**
     * Returns default value for {@link $laravelLogger}
     *
     * @return Logger logger instance.
     */
    protected function defaultLaravelLogger(): Logger
    {
        return \Illuminate\Support\Facades\Log::getFacadeRoot();
    }

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            [$text, $level, $category, $timestamp] = $message;
            $context = [
                'time' => $timestamp,
                'category' => $category,
            ];

            if (! is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable) {
                    $context['exception'] = $text;
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }

            $this->getLaravelLogger()->log($level, $text, $context);
        }
    }

    /**
     * Converts Yii log level into PSR one.
     *
     * @param  int  $level Yii log level.
     * @return string PSR log level.
     */
    protected function convertLogLevel($level): string
    {
        $matches = [
            Logger::LEVEL_ERROR => LogLevel::ERROR,
            Logger::LEVEL_WARNING => LogLevel::WARNING,
            Logger::LEVEL_INFO => LogLevel::INFO,
            Logger::LEVEL_TRACE => LogLevel::DEBUG,
            Logger::LEVEL_PROFILE => LogLevel::DEBUG,
            Logger::LEVEL_PROFILE_BEGIN => LogLevel::DEBUG,
            Logger::LEVEL_PROFILE_END => LogLevel::DEBUG,
        ];

        if (isset($matches[$level])) {
            return $matches[$level];
        }

        return LogLevel::INFO;
    }
}
