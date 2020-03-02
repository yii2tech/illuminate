<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Di;

use Illuminate\Contracts\Container\Container as ContainerContract;

/**
 * Container is an enhanced version of Yii DI container, which allows retrieving bindings from Laravel one.
 *
 * Laravel container's bindings take precedence over Yii one's definitions.
 *
 * Container can be setup at application entry script:
 *
 * ```php
 * <?php
 *
 * Yii::$container = new Yii2tech\Illuminate\Yii\Di\Container();
 *
 * $config = require(__DIR__ . '/../config/web.php');
 *
 * (new yii\web\Application($config))->run();
 * ```
 *
 * @see \Illuminate\Contracts\Container\Container
 *
 * @property \Illuminate\Contracts\Container\Container $illuminateContainer related Laravel DI container.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Container extends \yii\di\Container
{
    /**
     * @var \Illuminate\Contracts\Container\Container related Laravel DI container.
     */
    private $_illuminateContainer;

    /**
     * @return \Illuminate\Contracts\Container\Container
     */
    public function getIlluminateContainer(): ContainerContract
    {
        if ($this->_illuminateContainer === null) {
            $this->_illuminateContainer = $this->defaultIlluminateContainer();
        }

        return $this->_illuminateContainer;
    }

    /**
     * @param  \Illuminate\Contracts\Container\Container  $illuminateContainer
     * @return static self reference.
     */
    public function setIlluminateContainer(ContainerContract $illuminateContainer): self
    {
        $this->_illuminateContainer = $illuminateContainer;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\Container\Container default Laravel DI container.
     */
    protected function defaultIlluminateContainer(): ContainerContract
    {
        return \Illuminate\Container\Container::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function get($class, $params = [], $config = [])
    {
        if ($this->getIlluminateContainer()->has($class)) {
            return $this->getIlluminateContainer()->get($class);
        }

        return parent::get($class, $params, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function has($class): bool
    {
        if ($this->getIlluminateContainer()->has($class)) {
            return true;
        }

        return parent::has($class);
    }
}
