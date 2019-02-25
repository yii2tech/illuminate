Session and Authentication
==========================

One of the main problems running "Two-headed Beast" is having two different HTTP sessions in two different
web applications. If Laravel starts its own session and Yii starts another one, they will operate different
session data, which will cause inconsistent behavior while browsing pages rendered by two different frameworks.
In particular: user logged in with Laravel, will become anonymous at Yii and vice versa.

You can make your Yii application use the session provided by Laravel, using [[\Yii2tech\Illuminate\Yii\Web\Session]] component.
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
