<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * InitialMigration is base migration for application transferred from Yii2.
 *
 * It imports database structure from raw SQL dump, performing clean up of the legacy DB migration history.
 *
 * In order to create SQL dump you should execute console command like the following on:
 *
 * ```
 * mysqldump -d -u <username> -p<password> -h <hostname> <dbname>
 * ```
 *
 * Then copy content of the created dump file into '<<<SQL' block inside this migration.
 */
class InitialMigration extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop legacy Yii migration history table:
        // adjust table name, if necessary
        Schema::dropIfExists('migration');

        // If some table from the dump already exist, its import should be skipped
        // adjust table name, if necessary
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::getConnection()->statement(<<<SQL
# Your SQL dump lines here

CREATE TABLE `users`
(
   `id` integer NOT NULL AUTO_INCREMENT,
   `name` varchar(255) NOT NULL,
   `email` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE InnoDB;

# ...
SQL
        );
    }
}
