<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Web;

use Yii;
use yii\web\HeaderCollection;
use yii\base\InvalidConfigException;
use Illuminate\Http\Request as IlluminateRequest;

/**
 * Request uses Laravel HTTP request as an input source.
 *
 * This class allows avoiding problems when request handling requires raw body reading.
 * Also it provides some useful methods from Laravel request, which can be used in Yii.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'request' => [
 *             'class' => Yii2tech\Illuminate\Yii\Web\Request::class,
 *         ],
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @see \Illuminate\Http\Request
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Request extends \yii\web\Request
{
    /**
     * @var \Illuminate\Http\Request related Laravel HTTP request.
     */
    private $_illuminateRequest;

    /**
     * @var \yii\web\HeaderCollection request headers.
     */
    private $_headers;

    /**
     * @var string raw body content.
     */
    private $_rawBody;

    /**
     * @var array|null the request body parameters.
     */
    private $_bodyParams;

    /**
     * @return \Illuminate\Http\Request
     */
    public function getIlluminateRequest(): IlluminateRequest
    {
        if ($this->_illuminateRequest === null) {
            $this->_illuminateRequest = $this->defaultIlluminateRequest();
        }

        return $this->_illuminateRequest;
    }

    /**
     * @param  \Illuminate\Http\Request  $illuminateRequest
     * @return static self reference.
     */
    public function setIlluminateRequest(IlluminateRequest $illuminateRequest): self
    {
        $this->_illuminateRequest = $illuminateRequest;

        return $this;
    }

    /**
     * @return \Illuminate\Http\Request default related Laravel HTTP request.
     */
    protected function defaultIlluminateRequest(): IlluminateRequest
    {
        return \Illuminate\Container\Container::getInstance()->make('request');
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        if ($this->_headers === null) {
            $this->_headers = new HeaderCollection();
            foreach ($this->getIlluminateRequest()->headers->all() as $name => $values) {
                $this->_headers->set($name, $values);
            }
        }

        return $this->_headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->getIlluminateRequest()->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawBody()
    {
        if ($this->_rawBody === null) {
            $this->_rawBody = $this->getIlluminateRequest()->getContent();
        }

        return $this->_rawBody;
    }

    /**
     * {@inheritdoc}
     */
    public function setRawBody($rawBody): void
    {
        $this->_rawBody = $rawBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyParams()
    {
        if ($this->_bodyParams === null) {
            $illuminateRequest = $this->getIlluminateRequest();
            if ($illuminateRequest->isJson()) {
                $this->_bodyParams = $illuminateRequest->json()->all();
            } elseif ($this->getContentType() === 'application/x-www-form-urlencoded') {
                $this->_bodyParams = $illuminateRequest->request->all();
            } else {
                $this->_bodyParams = parent::getBodyParams();
            }
        }

        return $this->_bodyParams;
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyParams($values)
    {
        $this->_bodyParams = $values;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadCookies()
    {
        $cookies = [];
        if ($this->enableCookieValidation) {
            if ($this->cookieValidationKey == '') {
                throw new InvalidConfigException(get_class($this) . '::$cookieValidationKey must be configured with a secret key.');
            }
            foreach ($this->getIlluminateRequest()->cookies as $name => $value) {
                if (! is_string($value)) {
                    continue;
                }
                $data = Yii::$app->getSecurity()->validateData($value, $this->cookieValidationKey);
                if ($data === false) {
                    continue;
                }
                $data = @unserialize($data);
                if (is_array($data) && isset($data[0], $data[1]) && $data[0] === $name) {
                    $cookies[$name] = Yii::createObject([
                        'class' => \yii\web\Cookie::class,
                        'name' => $name,
                        'value' => $data[1],
                        'expire' => null,
                    ]);
                }
            }
        } else {
            foreach ($this->getIlluminateRequest()->cookies as $name => $value) {
                $cookies[$name] = Yii::createObject([
                    'class' => \yii\web\Cookie::class,
                    'name' => $name,
                    'value' => $value,
                    'expire' => null,
                ]);
            }
        }

        return $cookies;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|null  $keys input keys to retrieve, `null` means all input.
     * @return array input data.
     */
    public function all($keys = null): array
    {
        return $this->getIlluminateRequest()->all($keys);
    }

    /**
     * Runs Laravel validation on request data.
     * @see \Illuminate\Foundation\Providers\FoundationServiceProvider::registerRequestValidation()
     * @see \Illuminate\Validation\Factory::validate()
     *
     * @param  array  $rules validation rules.
     * @param  array  $messages error messages.
     * @param  array  $customAttributes
     * @return array  validated data.
     *
     * @throws \Illuminate\Validation\ValidationException if validation fails.
     */
    public function validate(array $rules, array $messages = [], array $customAttributes = []): array
    {
        return $this->getIlluminateRequest()->validate($rules, $messages, $customAttributes);
    }
}
