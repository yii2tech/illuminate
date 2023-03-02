<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use yii\log\Logger;

/**
 * Illuminated trait provides bridge to Laravel logger.
 *
 * @see \Illuminate\Log\Logger
 *
 * @mixin \yii\base\Component
 *
 * @property \Psr\Log\LoggerInterface $illuminateLogger related Laravel logger instance.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
trait Illuminated
{
    /**
     * @var \Illuminate\Log\Logger laravel logger instance.
     */
    private $_illuminateLogger;

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getIlluminateLogger(): LoggerInterface
    {
        if ($this->_illuminateLogger === null) {
            $this->_illuminateLogger = $this->defaultIlluminateLogger();
        }

        return $this->_illuminateLogger;
    }

    /**
     * @param  \Psr\Log\LoggerInterface  $laravelLogger
     * @return static self reference.
     */
    public function setIlluminateLogger(LoggerInterface $laravelLogger): self
    {
        $this->_illuminateLogger = $laravelLogger;

        return $this;
    }

    /**
     * Returns default value for {@see $illuminateLogger}
     *
     * @return \Psr\Log\LoggerInterface logger instance.
     */
    protected function defaultIlluminateLogger(): LoggerInterface
    {
        return \Illuminate\Support\Facades\Log::getFacadeRoot();
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
