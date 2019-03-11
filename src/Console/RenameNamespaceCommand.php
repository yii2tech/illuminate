<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Console;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Illuminate\Console\ConfirmableTrait;

/**
 * RenameNamespaceCommand allows namespace renaming over the group of files.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 1.0
 */
class RenameNamespaceCommand extends Command
{
    use ConfirmableTrait;

    /**
     * {@inheritdoc}
     */
    protected $name = 'namespace:rename';

    /**
     * {@inheritdoc}
     */
    protected $signature = 'namespace:rename {path}
                    {--from=app : Namespace to be renamed}
                    {--to=legacy : New namespace name}
                    {--force : Force the operation to run when in production}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Renames root namespace around PHP files in the specified directory.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->argument('path');

        $namespaceFrom = trim($this->option('from'), '\\');
        $namespaceTo = trim($this->option('to'), '\\');

        if (! $this->confirmToProceed("Namespace '{$namespaceFrom}' will be changed to '{$namespaceTo}' will be replaced at '{$path}' files.")) {
            return;
        }

        $totalCount = 0;
        $modifiedCount = 0;

        foreach ($this->findFiles($path) as $file) {
            $totalCount++;

            if ($this->renameNamespace($file, $namespaceFrom, $namespaceTo)) {
                $this->line('Modified: '.$file->getPathname());
                $modifiedCount++;
            }
        }

        $this->info("Processed: {$totalCount} files. Modified: {$modifiedCount} files.");
    }

    /**
     * Finds the files for the replacement.
     *
     * @param  string  $path path to directory to be searched.
     * @return \Iterator|\Symfony\Component\Finder\SplFileInfo[] found files.
     */
    protected function findFiles($path): iterable
    {
        return Finder::create()
            ->files()
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->name('*.php')
            ->in($path)
            ->getIterator();
    }

    /**
     * Renames namespace usages in given PHP file.
     *
     * @param  string  $file file name.
     * @param  string  $namespaceFrom namespace to be renamed
     * @param  string  $namespaceTo new namespace name.
     * @return bool whether file has been updated or not.
     */
    protected function renameNamespace(string $file, string $namespaceFrom, string $namespaceTo): bool
    {
        $content = file_get_contents($file);

        $newContent = preg_replace_callback('/^(namespace\\s+)(\\\\?'.preg_quote($namespaceFrom).')([^\\s]*;)(\\s*)$/m', function ($matches) use ($namespaceTo) {
            return $matches[1].$namespaceTo.$matches[3].$matches[4];
        }, $content);

        $newContent = preg_replace_callback('/^(use\\s+)(\\\\?'.preg_quote($namespaceFrom).')([^\\s]*;)(\\s*)$/m', function ($matches) use ($namespaceTo) {
            return $matches[1].$namespaceTo.$matches[3].$matches[4];
        }, $newContent);

        $newContent = preg_replace_callback('/(\\\\?)('.preg_quote($namespaceFrom).')(\\\\[^\\s]+::class)/m', function ($matches) use ($namespaceTo) {
            return $matches[1].$namespaceTo.$matches[3];
        }, $newContent);

        if (sha1($content) !== sha1($newContent)) {
            file_put_contents($file, $newContent);

            return true;
        }

        return false;
    }
}
