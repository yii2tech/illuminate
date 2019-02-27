<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\I18n;

use Illuminate\Support\Str;
use Illuminate\Translation\Translator;

/**
 * I18n allows message translation directly through Laravel translator.
 *
 * Categories, which translations should be passed to Laravel, are determined via {@link $illuminateCategories}.
 * Actual translation key will be composed by concatenation of category, dot symbol ('.') and message.
 * E.g. Yii translation call `Yii::t('category', 'message')` equals to `__('category.message')`.
 *
 * In addition, this class provides Laravel-like placeholders replacement, e.g. the ones marked by ':' symbol.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'i18n' => [
 *             'class' => Yii2tech\Illuminate\Yii\I18n\I18n::class,
 *             'illuminateCategories' => [
 *                 'auth',
 *                 'validation',
 *             ],
 *         ],
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @see \Illuminate\Translation\Translator
 *
 * @property \Illuminate\Translation\Translator $illuminateTranslator related Laravel translator.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class I18n extends \yii\i18n\I18N
{
    /**
     * @var string[] list of translation categories, which should be passed to {@link $illuminateTranslator}.
     * Messages from these categories will be translated directly via Laravel translator without involving Yii.
     * Translation message key will be composed by concatenation of category, dot symbol ('.') and message.
     */
    public $illuminateCategories = [];

    /**
     * @var \Illuminate\Translation\Translator related Laravel translator.
     */
    private $_illuminateTranslator;

    /**
     * @return \Illuminate\Translation\Translator
     */
    public function getIlluminateTranslator(): Translator
    {
        if ($this->_illuminateTranslator === null) {
            $this->_illuminateTranslator = $this->defaultIlluminateTranslator();
        }

        return $this->_illuminateTranslator;
    }

    /**
     * @param  \Illuminate\Translation\Translator  $illuminateTranslator
     * @return static self reference.
     */
    public function setIlluminateTranslator(Translator $illuminateTranslator): self
    {
        $this->_illuminateTranslator = $illuminateTranslator;

        return $this;
    }

    /**
     * @return Translator default Laravel translator.
     */
    protected function defaultIlluminateTranslator(): Translator
    {
        return \Illuminate\Container\Container::getInstance()->make('translator');
    }

    /**
     * {@inheritdoc}
     */
    public function translate($category, $message, $params, $language): string
    {
        if (in_array($category, $this->illuminateCategories, true)) {
            return $this->getIlluminateTranslator()->getFromJson($category.'.'.$message, $params, $language);
        }

        return parent::translate($category, $message, $params, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function format($message, $params, $language): string
    {
        $params = (array) $params;

        $message = parent::format($message, $params, $language);

        return $this->makeReplacements($message, $params);
    }

    /**
     * Make the Laravel-like place-holder replacements on a translated message.
     * It replaces placeholders, marked by ':', which are not processed with original {@link format()} method.
     *
     * @param  string  $message raw message.
     * @param  array  $params the parameters that will be used for the replacement.
     * @return string the formatted message.
     */
    protected function makeReplacements($message, array $params)
    {
        if (empty($params)) {
            return $message;
        }

        uksort($params, function ($a, $b) {
            if (mb_strlen($a) > mb_strlen($b)) {
                return -1;
            }

            return 1;
        });

        foreach ($params as $key => $value) {
            $message = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $message
            );
        }

        return $message;
    }
}
