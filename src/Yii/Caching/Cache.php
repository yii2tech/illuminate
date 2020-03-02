<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2019 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Yii\Caching;

use Illuminate\Contracts\Cache\Repository;

/**
 * Cache implements a cache application component using Laravel cache repository.
 *
 * This class allows sharing of the cache engine between Yii and Laravel applications, removing
 * necessity of double configuration and extra cache storage connections.
 *
 * Application configuration example:
 *
 * ```php
 * return [
 *     'components' => [
 *         'cache' => Yii2tech\Illuminate\Yii\Caching\Cache::class,
 *         // ...
 *     ],
 *     // ...
 * ];
 * ```
 *
 * > Note: by default this component will not allow you to share particular cache keys between Yii and Laravel,
 *   since Yii uses special prefix for the cache keys and stores data in serialized state. If you wish to share same
 *   cache key you should disable {@see \yii\caching\Cache::$keyPrefix} and {@see \yii\caching\Cache::$serializer}.
 *
 * @see \yii\caching\Cache::$keyPrefix
 * @see \yii\caching\Cache::$serializer
 * @see \Illuminate\Contracts\Cache\Repository
 *
 * @property \Illuminate\Contracts\Cache\Repository $illuminateCache related Laravel cache repository.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class Cache extends \yii\caching\Cache
{
    /**
     * @var \Illuminate\Contracts\Cache\Repository related Laravel cache repository.
     */
    private $_illuminateCache;

    /**
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function getIlluminateCache(): Repository
    {
        if ($this->_illuminateCache === null) {
            $this->_illuminateCache = $this->defaultIlluminateCache();
        }

        return $this->_illuminateCache;
    }

    /**
     * @param  \Illuminate\Contracts\Cache\Repository  $illuminateCache
     * @return static self reference.
     */
    public function setIlluminateCache(Repository $illuminateCache): self
    {
        $this->_illuminateCache = $illuminateCache;

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\Cache\Repository default cache repository.
     */
    protected function defaultIlluminateCache(): Repository
    {
        return \Illuminate\Support\Facades\Cache::getFacadeRoot();
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue($key)
    {
        return $this->getIlluminateCache()->get($key, false);
    }

    /**
     * {@inheritdoc}
     */
    protected function setValue($key, $value, $duration): bool
    {
        $this->getIlluminateCache()->put($key, $value, $this->convertDuration($duration));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function addValue($key, $value, $duration): bool
    {
        return $this->getIlluminateCache()->add($key, $value, $this->convertDuration($duration));
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteValue($key): bool
    {
        return $this->getIlluminateCache()->forget($key);
    }

    /**
     * {@inheritdoc}
     */
    protected function flushValues(): bool
    {
        return $this->getIlluminateCache()->clear();
    }

    /**
     * {@inheritdoc}
     */
    protected function getValues($keys)
    {
        return $this->getIlluminateCache()->getMultiple($keys, false);
    }

    /**
     * {@inheritdoc}
     */
    protected function setValues($data, $duration): array
    {
        $this->getIlluminateCache()->setMultiple($data, $this->convertDuration($duration));

        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function addValues($data, $duration): array
    {
        $values = $this->multiGet(array_keys($data));

        $failedKeys = [];
        $newValues = [];

        foreach ($values as $key => $value) {
            if ($value !== false) {
                $failedKeys[] = $key;
                continue;
            }

            $newValues[$key] = $data[$key];
        }

        $this->setValues($newValues, $duration);

        return $failedKeys;
    }

    /**
     * Converts cache duration specification from Yii to Laravel.
     *
     * @param  float|int|null  $duration cache duration in seconds, zero - means infinite.
     * @return float|int|null cache duration in seconds, `null` means infinite.
     */
    protected function convertDuration($duration)
    {
        return $duration == 0 ? null : $duration;
    }
}
