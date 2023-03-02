<?php

namespace Yii2tech\Illuminate\Test\Yii\Caching;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Factory;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Caching\Cache;

class CacheTest extends TestCase
{
    public function testStore()
    {
        $cache = (new Cache())->setIlluminateCache(new Repository(new ArrayStore()));

        $this->assertTrue($cache->set('key', 'initial'));
        $this->assertSame('initial', $cache->get('key'));
        $this->assertSame(false, $cache->get('undefined'));
        $this->assertTrue($cache->exists('key'));
        $this->assertFalse($cache->exists('undefined'));

        $this->assertTrue($cache->add('new', 'new'));
        $this->assertSame('new', $cache->get('new'));
        $this->assertFalse($cache->add('new', 'update'));
        $this->assertSame('new', $cache->get('new'));

        $this->assertTrue($cache->delete('new'));
        $this->assertFalse($cache->exists('new'));

        $this->assertTrue($cache->flush());
        $this->assertFalse($cache->exists('key'));
    }

    public function testStoreMultiple()
    {
        $cache = (new Cache())->setIlluminateCache(new Repository(new ArrayStore()));

        $this->assertSame([], $cache->multiSet(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2'], $cache->multiGet(['key1', 'key2']));

        $this->assertSame(['key1'], $cache->multiAdd(['key1' => 'value1', 'key3' => 'value3']));
        $this->assertSame('value3', $cache->get('key3'));
    }

    public function testGetDefaultIlluminateCache()
    {
        $cacheManagerMock = $this->getMockBuilder(Factory::class)
            ->onlyMethods(['store'])
            ->getMock();

        $cacheManagerMock->method('store')->willReturn(new Repository(new ArrayStore()));

        $this->app->instance('cache', $cacheManagerMock);

        $cache = new Cache();

        $this->assertSame($cacheManagerMock->store(), $cache->getIlluminateCache());
    }
}
