Session and Authentication
==========================

Session and Web User <span id="session-and-web-user"></span>
--------------------

One of the main problems running "Two-headed Beast" is having two different HTTP sessions in two different
web applications. If Laravel starts its own session and Yii starts another one, they will operate different
session data, which will cause inconsistent behavior while browsing pages, rendered by two different frameworks.
In particular: user logged in with Laravel, will become anonymous at Yii and vice versa.

You can make your Yii application use the session provided by Laravel, using `\Yii2tech\Illuminate\Yii\Web\Session` component.
Yii application configuration example:

```php
<?php

return [
    'components' => [
        'session' => Yii2tech\Illuminate\Yii\Web\Session::class,
        // ...
    ],
    // ...
];
```

**Heads up!** usage of this component requires Yii application running within `\Illuminate\Session\Middleware\StartSession` middleware.
Make sure it is running prior to `\Yii2tech\Illuminate\Http\YiiApplicationMiddleware` one.

With such configuration session, data stored by Laravel will be available in Yii and vice versa.
However, this is not enough to keep user authentication state. To make it work `\yii\web\User::$idParam` should be
pointed to the same session key Laravel use to store authenticated user ID. You can use following configuration to
achieve this:

```php
<?php

return [
    'components' => [
        'session' => Yii2tech\Illuminate\Yii\Web\Session::class,
        'user' => [
            'idParam' => app()->make('auth')->guard('web')->getName(),
        ],
        // ...
    ],
    // ...
];
```

However, it is much more reliable to use `\Yii2tech\Illuminate\Yii\Web\User` class as Yii web user component.
This class will use Laravel authentication guard directly for user retrieving and storing. This solves authentication
tracking problem as well as allows usage of any other authentication guard provided by Laravel besides session.
Yii application configuration example:

```php
<?php

return [
    'components' => [
        'session' => Yii2tech\Illuminate\Yii\Web\Session::class,
        'user' => Yii2tech\Illuminate\Yii\Web\User::class,
        // ...
    ],
    // ...
];
```

**Heads up!** Authentication handle tools, provided by this package, will serve you well in "reading" mode,
but may still create inconsistences in "writing" mode. It is better to migrate all your code related to user
identity switching (e.g. login, logout, singup and so on) into Laravel application as soon as possible.

Keep in mind that you might need to adjust Laravel "auth.providers" configuration to match your legacy database. For example,
table, which store identity records, most likely called "user" (in singular form), while Laravel uses name "users" (in plural), 
you probably store hashed password inside field "password_hash", while Laravel uses simply "password" and so on.


Password hashing <span id="password-hashing"></span>
----------------

Luckily for you, both Yii and Laravel provides same algorithm for password hashing. In case you have used `\yii\base\Security::generatePasswordHash()`
to hash your user's password and `\yii\base\Security::validatePassword()` for its verification, you will be able to use 
`\Illuminate\Hashing\BcryptHasher::make()` and `\Illuminate\Hashing\BcryptHasher::check()` correspondingly.
All you need to do is ensuring correct configuration for hashing in your Laravel application. For example:

```php
<?php
// file 'config/hashing.php'

return [
    'driver' => 'bcrypt',
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 13), // should match `yii\base\Security::$passwordHashCost`
    ],
    // ...
];
```
