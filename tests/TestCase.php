<?php

namespace Yii2tech\Illuminate\Test;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Yii;
use yii\helpers\ArrayHelper;
use Illuminate\Filesystem\Filesystem;

/**
 * Base class for the test cases.
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Container Laravel application mock.
     */
    protected $app;

    /**
     * @var \Illuminate\Filesystem\Filesystem file system helper.
     */
    private $fileSystem;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockLaravelApplication();
        $this->mockYiiApplication();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->destroyYiiApplication();
        $this->removeTestFilePath();
    }

    /**
     * @return Filesystem file system helper.
     */
    protected function getFileSystem(): Filesystem
    {
        if ($this->fileSystem === null) {
            $this->fileSystem = new Filesystem();
        }

        return $this->fileSystem;
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Creates new Laravel application.
     */
    protected function mockLaravelApplication()
    {
        $this->app = new Container();

        $db = new \Illuminate\Database\Capsule\Manager;

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $db->bootEloquent();
        $db->setAsGlobal();

        $this->app->instance('db', $db);

        Container::setInstance($this->app);
        Facade::setFacadeApplication($this->app);

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }

    /**
     * Populates `Yii::$app` with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockYiiApplication($config = [], $appClass = \yii\console\Application::class)
    {
        Yii::$app = new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => \Yii2tech\Illuminate\Yii\Db\Connection::class,
                ],
            ],
        ], $config));
    }

    /**
     * Destroys application in `Yii::$app` by setting it to null.
     */
    protected function destroyYiiApplication()
    {
        Yii::$app = null;
        Yii::$app = new \yii\di\Container();
    }

    /**
     * @return string test file path
     */
    protected function getTestFilePath()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'tmp';
    }

    /**
     * Ensures test file path exists.
     *
     * @return string test file path
     */
    protected function ensureTestFilePath()
    {
        $path = $this->getTestFilePath();
        $this->getFileSystem()->makeDirectory($path);

        return $path;
    }

    /**
     * Removes the test file path.
     */
    protected function removeTestFilePath()
    {
        $path = $this->getTestFilePath();
        $this->getFileSystem()->deleteDirectory($path);
    }

    /**
     * Get a database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function getDbConnection()
    {
        return $this->app->get('db.connection');
    }

    /**
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function getSchemaBuilder()
    {
        return $this->getDbConnection()->getSchemaBuilder();
    }

    /**
     * Invokes object method, even if it is private or protected.
     *
     * @param  object  $object object.
     * @param  string  $method method name.
     * @param  array  $args method arguments
     * @return mixed method result
     */
    protected function invoke($object, $method, array $args = [])
    {
        $classReflection = new \ReflectionClass(get_class($object));
        $methodReflection = $classReflection->getMethod($method);

        $methodReflection->setAccessible(true);
        $result = $methodReflection->invokeArgs($object, $args);
        $methodReflection->setAccessible(false);

        return $result;
    }
}
