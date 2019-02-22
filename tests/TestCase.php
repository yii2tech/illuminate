<?php

namespace Yii2tech\Illuminate\Test;

use Yii;
use yii\helpers\ArrayHelper;
use Illuminate\Filesystem\Filesystem;

/**
 * Base class for the test cases.
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
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
     * Populates `Yii::$app` with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockYiiApplication($config = [], $appClass = \yii\console\Application::class)
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => \yii\db\Connection::class,
                    'dsn' => 'sqlite::memory:',
                ],
            ],
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Destroys application in `Yii::$app` by setting it to null.
     */
    protected function destroyYiiApplication()
    {
        Yii::$app = null;
        Yii::$container = null;
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
