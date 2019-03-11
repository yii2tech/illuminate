<?php

namespace Yii2tech\Illuminate\Test\Console;

use Yii2tech\Illuminate\Test\TestCase;
use Yii2tech\Illuminate\Console\RenameNamespaceCommand;

class RenameNamespaceCommandTest extends TestCase
{
    /**
     * @see RenameNamespaceCommand::renameNamespace()
     */
    public function testRenameNamespace()
    {
        $testFilePath = $this->ensureTestFilePath();

        $srcFile = realpath($this->getVendorPath().'/yiisoft/yii2/widgets/ActiveField.php');
        $file = $testFilePath.DIRECTORY_SEPARATOR.'ActiveField.php';

        copy($srcFile, $file);

        $command = new RenameNamespaceCommand();

        $this->assertTrue($this->invoke($command, 'renameNamespace', [$file, 'yii', 'legacy']));

        $fileContent = file_get_contents($file);

        $this->assertFalse(strpos($fileContent, 'namespace yii'));
        $this->assertFalse(strpos($fileContent, 'use yii'));
        $this->assertTrue(preg_match('/yii[^\\s]+::class/m', $fileContent) === 0);

        $this->assertTrue(strpos($fileContent, 'namespace legacy') > 0);
        $this->assertTrue(strpos($fileContent, 'use legacy') > 0);
        $this->assertTrue(preg_match('/legacy[^\\s]+::class/m', $fileContent) > 0);
    }

    /**
     * @see RenameNamespaceCommand::findFiles()
     */
    public function testFindFiles()
    {
        $command = new RenameNamespaceCommand();

        $files = $this->invoke($command, 'findFiles', [$this->getVendorPath().'/yiisoft/yii2-composer']);

        $this->assertCount(2, $files);

        $files = iterator_to_array($files);
        $file = array_pop($files);
        $this->assertTrue(strpos($file->getFilename(), '.php') > 0);
    }
}
