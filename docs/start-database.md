Database
========

Database Migrations <span id="database-migrations"></span>
-------------------

Both Yii and Laravel uses a migration scripts approach for database structure versioning.
Unfortunally, libraries used for such scripts are incompatible in these frameworks.
You'll have to abandon migration history, created during Yii application development and replace it with the new Laravel one.

The easiest way to do so is usage of the DDL SQL dump of your existing database, putting it inside single initial migration.
At first, create a database dump without data:

```
mysqldump -d -u <username> -p<password> -h <hostname> <dbname>
```

Then generate new Laravel migration:

```
php artisan make:migration InitialMigration
```

Write up the migration code in the way it will execute content of previously created database dump as a single large SQL query.
Such migration class may look like following:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class InitialMigration extends Migration
{
    public function up(): void
    {
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
```

**Heads up!** Both Laravel and Yii store DB migration history at the separated table with special structure.
By default this table named 'migration' in Yii and 'migrations' in Laravel. Still you should re-check there is no
naming conflict between these two tables. Adjust the value of Laravel configuration key 'database.migrations', if there is.

The migration we have created above is not very practical in terms of project framework transfer. Attempt to execute
Laravel DB migration mechanism at production server will likely cause SQL error saying "table users is already exist".
Also former Yii migration history remains, polluting the database. Thus we should make some adjustments to the migration code:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class InitialMigration extends Migration
{
    public function up(): void
    {
        // Drop the legacy Yii migration history table:
        Schema::dropIfExists('migration');
        
        if (Schema::hasTable('users')) {
            // If some table from the dump already exist, its import should be skipped
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
```

You may create a scaffold for such migration using the following console command:

```
php artisan vendor:publish --provider="Yii2tech\Illuminate\YiiIlluminateServiceProvider" --tag=migrations
```

Just remember you should adjust the created migration class.


Shared Database Connection <span id="shared-database-connection"></span>
--------------------------

Allowing Laravel and Yii establish their own database connections causes two major problems.
First of all, database server connection establishing consumes much time. Performing it twice during
single HTTP request handling may significantly decrease performance. Also this doubles stress taken by
database server, decreasing its performance as well. But more crucial thing is running database transactions:
in case transaction is opened by Laravel DB connection any query performed via Yii one will fall out of transaction.
For example:

```php
<?php

use Illuminate\Support\Facades\DB;

DB::beginTransaction();

DB::table('users')->update(['votes' => 0]); // will be reverted on 'rollback'
Yii::$app->db->createCommand()->delete('votes', ['status' => 1])->execute(); // will NOT be reverted on 'rollback'!

DB::rollBack();
```

You can avoid all these troubles using `\Yii2tech\Illuminate\Yii\Db\Connection` class for your Yii DB connection.
This class allows sharing of the `\PDO` instance between Laravel and Yii DB connections, ensuring single connection
establishment, consistent transaction handling and eliminating necessity to configure database parameters in both
applications. Yii application configuration example:

```php
<?php

return [
    'components' => [
        'db' => Yii2tech\Illuminate\Yii\Db\Connection::class,
        // ...
    ],
    // ...
];
```
