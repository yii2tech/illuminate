Caching
=======

Having two different cache storages defined by Yii and Laravel causes similar problems described for database connection.
You will have to duplicate configuration for cache components, extra connections to cache servers may appear and so on.
This package provides `\Yii2tech\Illuminate\Yii\Caching\Cache` component for Yii to avoid such problems. This cache
component uses Laravel cache repository as a storage, transferring all cache operations throw it.

Application configuration example:

```php
<?php

return [
    'components' => [
        'cache' => Yii2tech\Illuminate\Yii\Caching\Cache::class,
        // ...
    ],
    // ...
];
```

**Heads up!** By default this component will not allow you to share particular cache keys between Yii and Laravel,
since Yii uses special prefix for the cache keys and stores data in serialized state. If you wish to share same
cache key you should disable `\yii\caching\Cache::$keyPrefix` and `\yii\caching\Cache::$serializer`. However,
such practice is not recommended: in case you need to share cache key from Laravel to Yii, it is better to access
Laravel cache storage directly, for example using `\Illuminate\Support\Facades\Cache` facade.
