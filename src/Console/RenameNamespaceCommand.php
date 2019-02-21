<?php
/**
 * @link https://github.com/yii2tech
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 */

namespace Yii2tech\Illuminate\Console;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

/**
 * RenameNamespaceCommand
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
    protected $name = 'yii:rename-namespace';

    /**
     * {@inheritdoc}
     */
    protected $signature = 'yii:rename-namespace
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
        ;
    }
}
