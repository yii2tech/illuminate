<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Web;

use ArrayIterator;
use yii\base\Component;
use Illuminate\Session\Store;

/**
 * Session allows usage of the Laravel Session for Yii one.
 *
 * This class allows sharing session data between Laravel and Yii, preserving authentication state loss and
 * avoiding session data loss.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'session' => Yii2tech\Illuminate\Yii\Web\Session::class,
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * @see \Illuminate\Session\Store
 *
 * @property \Illuminate\Session\Store $laravelSession related Laravel session instance.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Session extends \yii\web\Session
{
    /**
     * {@inheritdoc}
     */
    public $flashParam = '__yii_flash';

    /**
     * @var \Illuminate\Session\Store
     */
    private $_laravelSession;

    /**
     * @return \Illuminate\Session\Store Laravel session store.
     */
    public function getLaravelSession(): Store
    {
        if ($this->_laravelSession === null) {
            $this->_laravelSession = $this->defaultLaravelSession();
        }

        return $this->_laravelSession;
    }

    /**
     * @param \Illuminate\Session\Store $session Laravel session store.
     * @return static self reference.
     */
    public function setLaravelSession(Store $session): self
    {
        $this->_laravelSession = $session;

        return $this;
    }

    /**
     * @return Store session store instance.
     */
    protected function defaultLaravelSession(): Store
    {
        return \Illuminate\Support\Facades\Session::getFacadeRoot();
    }

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
    public function open(): void
    {
        if ($this->getIsActive()) {
            return;
        }

        $this->getLaravelSession()->start();
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        if ($this->getIsActive()) {
            $this->getLaravelSession()->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        if ($this->getIsActive()) {
            $this->getLaravelSession()->invalidate();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive(): bool
    {
        return $this->getLaravelSession()->isStarted();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getLaravelSession()->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($value): void
    {
        $this->getLaravelSession()->setId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function regenerateID($deleteOldSession = false): void
    {
        if ($this->getIsActive()) {
            $this->getLaravelSession()->regenerate($deleteOldSession);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getLaravelSession()->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value): void
    {
        $this->getLaravelSession()->setName($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->open();

        return new ArrayIterator($this->getLaravelSession()->all());
    }

    /**
     * {@inheritdoc}
     */
    public function getCount(): int
    {
        $this->open();

        return count($this->getLaravelSession()->all());
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $defaultValue = null)
    {
        $this->open();

        return $this->getLaravelSession()->get($key, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->open();

        $this->getLaravelSession()->put($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $this->open();

        $value = $this->getLaravelSession()->get($key);

        $this->getLaravelSession()->forget($key);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(): void
    {
        $this->open();
        $this->getLaravelSession()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        $this->open();

        return $this->getLaravelSession()->has($key);
    }

    // Flash :

    /**
     * {@inheritdoc}
     */
    protected function updateFlashCounters(): void
    {
        $counters = $this->get($this->flashParam, []);
        if (is_array($counters)) {
            foreach ($counters as $key => $count) {
                if ($count > 0) {
                    unset($counters[$key]);
                    $this->remove($key);
                } elseif ($count == 0) {
                    $counters[$key]++;
                }
            }
            $this->set($this->flashParam, $counters);
        } else {
            // fix the unexpected problem that flashParam doesn't return an array
            $this->remove($this->flashParam);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        $counters = $this->get($this->flashParam, []);
        if (isset($counters[$key])) {
            $value = $this->get($key, $defaultValue);
            if ($delete) {
                $this->removeFlash($key);
            } elseif ($counters[$key] < 0) {
                // mark for deletion in the next request
                $counters[$key] = 1;
                $this->set($this->flashParam, $counters);
            }

            return $value;
        }

        return $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllFlashes($delete = false): array
    {
        $counters = $this->get($this->flashParam, []);
        $flashes = [];

        $session = $this->getLaravelSession()->all();

        foreach (array_keys($counters) as $key) {
            if (array_key_exists($key, $session)) {
                $flashes[$key] = $session[$key];
                if ($delete) {
                    unset($counters[$key], $session[$key]);
                    $this->remove($key);
                } elseif ($counters[$key] < 0) {
                    // mark for deletion in the next request
                    $counters[$key] = 1;
                }
            } else {
                unset($counters[$key]);
            }
        }

        $this->set($this->flashParam, $counters);

        return $flashes;
    }

    /**
     * {@inheritdoc}
     */
    public function setFlash($key, $value = true, $removeAfterAccess = true): void
    {
        $counters = $this->get($this->flashParam, []);
        $counters[$key] = $removeAfterAccess ? -1 : 0;

        $this->set($key, $value);
        $this->set($this->flashParam, $counters);
    }

    /**
     * {@inheritdoc}
     */
    public function addFlash($key, $value = true, $removeAfterAccess = true): void
    {
        $counters = $this->get($this->flashParam, []);
        $counters[$key] = $removeAfterAccess ? -1 : 0;

        $this->set($this->flashParam, $counters);
        $session = $this->getLaravelSession()->all();

        if (empty($session[$key])) {
            $session[$key] = [$value];
        } elseif (is_array($session[$key])) {
            $session[$key][] = $value;
        } else {
            $session[$key] = [$session[$key], $value];
        }

        $this->set($key, $session[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFlash($key)
    {
        $counters = $this->get($this->flashParam, []);
        $session = $this->getLaravelSession()->all();
        $value = isset($session[$key], $counters[$key]) ? $session[$key] : null;
        unset($counters[$key]);
        $this->remove($key);
        $this->set($this->flashParam, $counters);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllFlashes(): void
    {
        $counters = $this->get($this->flashParam, []);
        foreach (array_keys($counters) as $key) {
            $this->remove($key);
        }
        $this->remove($this->flashParam);
    }

    /**
     * {@inheritdoc}
     */
    public function hasFlash($key): bool
    {
        return $this->getFlash($key) !== null;
    }

    // ArrayAccess :

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $this->open();
        $session = $this->getLaravelSession()->all();

        return isset($session[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $this->open();

        return $this->getLaravelSession()->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $item)
    {
        $this->open();
        $_SESSION[$offset] = $item;

        $this->getLaravelSession()->put($offset, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->open();

        $this->getLaravelSession()->forget($offset);
    }
}
