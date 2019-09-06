<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Log;

use yii\helpers\VarDumper;

/**
 * Target passes log entries to Laravel logger.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'log' => [
 *             'targets' => [
 *                 [
 *                     'class' => Yii2tech\Illuminate\Yii\Log\Target::class,
 *                 ],
 *                 // ...
 *             ],
 *         ],
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Target extends \yii\log\Target
{
    use Illuminated;

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

            $this->getIlluminateLogger()->log($this->convertLogLevel($level), $text, $context);
        }
    }
}
