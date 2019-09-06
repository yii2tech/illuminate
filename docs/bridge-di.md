Dependency Injection
====================

DI Container Bridge <span id="di-container-bridge"></span>
-------------------

Both Laravel and Yii provide dependency injection container in more or less similar way.
Container stores bindings (definitions) for classes and interfaces, which can be resolved by demand.
This package provides `\Yii2tech\Illuminate\Yii\Di\Container` as a replacement for the standard Yii container.
It connects Yii container with the Laravel one, allowing resolve of any binding defined at Laravel within Yii.
Container can be setup at application entry script. For example:

```php
<?php

Yii::$container = new Yii2tech\Illuminate\Yii\Di\Container(); // replace standard Yii DI container

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
```

Now any binding defined within Laravel DI container will be accessible via Yii one. For example:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(\HelpSpot\API::class, function ($app) {
            return new \HelpSpot\API($app->make('HttpClient'));
        });
    }
}

// ...
use Yii;
use Illuminate\Container\Container;

$laravelContainer = Container::getInstance();

$objectFromLaravel = $laravelContainer->get(\HelpSpot\API::class);
$objectFromYii = Yii::$container->get(\HelpSpot\API::class);
var_dump($objectFromLaravel === $objectFromYii); // outputs 'true'

$objectCreatedByYii = Yii::createObject(\HelpSpot\API::class);
var_dump($objectFromLaravel === $objectCreatedByYii); // outputs 'true'
```

> Note: you are able to access Laravel DI container anywhere within your Yii application, using `\Illuminate\Container\Container::getInstance()`.
  However, you are not allowed to use Yii DI container or service locator within Laravel application.

You may configure Yii DI container to be used via "yii.middleware.container" configuration key. If it is set, `\Yii2tech\Illuminate\Http\YiiApplicationMiddleware`
will bootstrap it automatically before running Yii application. Laravel configuration example:

```php
<?php
// file "config/yii.php"

return [
    'middleware' => [
        // ..
        'container' => [
            '__class' => Yii2tech\Illuminate\Yii\Di\Container::class,
        ],
    ],
];
```


Yii Components Transfer <span id="di-container-bridge"></span>
-----------------------

If you developed your Yii application right, its logic takes residence among models and separated components (services).
Thus your Yii application configuration looks like following:

```php
<?php

return [
    'components' => [
        'mailChimp' => [
            'class' => \legacy\components\MailChimp::class,
            'apiKey' => 'some-api-key',
            'apiSecret' => 'some-api-secret',
        ],
        'subscriptionManager' => [
            'class' => \legacy\components\SubscriptionManager::class,
            'trialPeriod' => 30,
        ],
        // ...
    ],
    // ...
];
```

In order to make "Two-headed Beast" configuration more consistent, you should move all your custom Yii components to Laravel.
Usage of `\Yii2tech\Illuminate\Yii\Di\Container` makes it easy enough. First of all, move the component class file from
"legacy" directory to somewhere withing "App" namespace, for example to the "Services" directory, updating namespace accordingly.
At this stage you should also consider removing `\yii\base\Component` inheritance, if it present. Thus your new Laravel
service may look like following:

```php
<?php

namespace App\Services;

class MailChimp
{
    public $apiKey;
    
    public $apiSecret;
    
    // ...
}

// ...

class SubscriptionManager
{
    public $trialPeriod;
    
    // ...
}
```

Next you need to create bindings for these components within Laravel DI container. Those can be put into a service provider.
For example:

```php
<?php

namespace App\Providers;

use App\Services\MailChimp;
use App\Services\SubscriptionManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MailChimp::class, function ($app) {
            $component = new MailChimp();
            $component->apiKey = config('mailchimp.apiKey');
            $component->apiSecret = config('mailchimp.apiSecret');
            return $component;
        });
        
        $this->app->singleton(SubscriptionManager::class, function ($app) {
            $component = new SubscriptionManager();
            $component->trialPeriod = config('subscription.trialPeriod');
            return $component;
        });
        
        // ...
    }
}
```

Obviously, you will have to create corresponding Laravel configuration files to support these bindings. In this example,
those will be "config/mailchimp.php" and "config/subscription.php".

Once it is done, you should adjust your Yii application configuration, removing redundant code from it. At result configuration
file will look like following:

```php
<?php

return [
    'components' => [
        'mailChimp' => \App\Services\MailChimp::class, // no need for configuration as it is handled by Laravel DI container
        'subscriptionManager' => \App\Services\SubscriptionManager::class, // service locator component refers to DI definition 
        // ...
    ],
    // ...
];
```

When it is finished, the code of your Yii application will know no difference: following invocations will function as before:

```php
<?php

Yii::$app->mailChimp->createNewsletter(/*...*/);
Yii::$app->subscriptionManager->subscribe(/*...*/);
```

> Tip: you can use [illuminatech/array-factory](https://github.com/illuminatech/array-factory), continuing usage of the familiar
  array syntax configuration for your components in Laravel.

In case your Yii application defines components via `\yii\di\Container::$definitions`, code transfer will be even more
easy for you: you will need simply to move definitions from Yii container to Laravel one.
