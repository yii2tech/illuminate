Upgrading Instructions for Yii2 to Laravel Migration Package
============================================================

!!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to following the instructions
for both A and B.

Upgrade from 1.2.1
------------------

* Minimal required PHP version has been raised to 8.0. Make sure to update your environment accordingly.


Upgrade from 1.1.2
------------------

* Virtual property `Yii2tech\Illuminate\Yii\Db\Connection::$laravelConnection` renamed to `Yii2tech\Illuminate\Yii\Db\Connection::$illuminateConnection`.
  Check references to this property and methods defining it in your code and fix them accordingly.

* Method `Yii2tech\Illuminate\Yii\Web\User::convertLaravelIdentity()` renamed to `Yii2tech\Illuminate\Yii\Web\User::convertIlluminateIdentity()`.
  Check references to this method in your code and fix them accordingly.


Upgrade from 1.0.0
------------------

* "illuminate/*" package requirements were raised to 6.0. Make sure to upgrade your code accordingly.
