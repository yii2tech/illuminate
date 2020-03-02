<?php

namespace Yii2tech\Illuminate\Test\Yii\Di;

use Yii;
use stdClass;
use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Yii\Di\Container;

class ContainerTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        Yii::$container = (new Container())->setIlluminateContainer($this->app);
    }

    public function testGet()
    {
        $this->app->singleton(stdClass::class, function () {
            return new stdClass();
        });

        $objectFromIlluminate = $this->app->get(stdClass::class);
        $objectFromYii = Yii::$container->get(stdClass::class);
        $this->assertSame($objectFromIlluminate, $objectFromYii);

        $objectCreatedByYii = Yii::createObject(stdClass::class);
        $this->assertSame($objectFromIlluminate, $objectCreatedByYii);
    }

    public function testHas()
    {
        $this->assertFalse(Yii::$container->has(stdClass::class));

        $this->app->singleton(stdClass::class, function () {
            return new stdClass();
        });
        $this->assertTrue(Yii::$container->has(stdClass::class));
    }

    public function testGetDefaultIlluminateContainer()
    {
        $container = new Container();

        $this->assertSame($this->app, $container->getIlluminateContainer());
    }
}
