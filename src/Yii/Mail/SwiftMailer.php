<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Mail;

/**
 * SwiftMailer is an advanced version of {@link \yii\swiftmailer\Mailer}, which allows sharing mailer/transport instance with Laravel mailer.
 *
 * Usage of this component allows you having single configuration for the mail transport throughout entire project.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'mailer' => Yii2tech\Illuminate\Yii\Mail\SwiftMailer::class,
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @see https://github.com/yiisoft/yii2-swiftmailer
 * @see \yii\swiftmailer\Mailer
 * @see \Illuminate\Mail\MailServiceProvider
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class SwiftMailer extends \yii\swiftmailer\Mailer
{
    /**
     * {@inheritdoc}
     */
    protected function createSwiftMailer()
    {
        return \Illuminate\Container\Container::getInstance()->make('swift.mailer');
    }
}
