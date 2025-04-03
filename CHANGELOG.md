Yii2 to Laravel Migration Package Change Log
============================================

1.3.2 Under Development
-----------------------

- Enh #15: Added support for "illuminate/*" 12.0 (klimov-paul)


1.3.1, March 25, 2024
---------------------

- Enh: Added support for "illuminate/*" 11.0 (klimov-paul)


1.3.0, March 2, 2023
--------------------

- Bug: Fixed compatibility with "symfony/http-foundation" 6.x for `DummyResponse::send()` (klimov-paul)
- Bug #13: Fixed `Yii2tech\Illuminate\Yii\Log\Logger` and `Yii2tech\Illuminate\Yii\Caching\Cache` unable to pick up default related Illuminate object (klimov-paul)
- Enh: Added support for "illuminate/support" 10.0 (klimov-paul)


1.2.1, February 9, 2022
-----------------------

- Enh: Added support for "illuminate/*" 9.0 (klimov-paul)


1.2.0, September 9, 2020
------------------------

- Enh: Added support for "illuminate/*" 8.0 (klimov-paul)
- Chg: Virtual property `Connection::$laravelConnection` renamed to `Connection::$illuminateConnection` (klimov-paul)
- Chg: Method `User::convertLaravelIdentity()` renamed to `User::convertIlluminateIdentity()` (klimov-paul)


1.1.2, July 24, 2020
--------------------

- Bug #8: Fixes environment determining (leandrogehlen)
- Enh #7: `Yii2tech\Illuminate\Yii\Web\Request` now picks up URI info from illuminate one (klimov-paul)


1.1.1, March 4, 2020
--------------------

- Bug #3: Fixed `Yii2tech\Illuminate\Yii\Di\Container` and `Yii2tech\Illuminate\Yii\Caching\Cache` unable to pick up default related Illuminate object (klimov-paul)
- Enh: Added support for "illuminate/*" 7.0 (klimov-paul)


1.1.0, September 6, 2019
------------------------

- Enh: Added support for "illuminate/*" 6.0 (klimov-paul)


1.0.0, March 11, 2019
---------------------

- Initial release.
