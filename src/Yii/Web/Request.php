<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Web;

use yii\web\HeaderCollection;
use Illuminate\Http\Request as IlluminateRequest;

/**
 * Request uses Laravel HTTP request as an input source.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'request' => Yii2tech\Illuminate\Yii\Web\Request::class,
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
}
