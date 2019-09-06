Mail
====

In case you are using [yiisoft/yii2-swiftmailer](https://github.com/yiisoft/yii2-swiftmailer) as mailing solution in your Yii application,
you certainly will use [illuminate/mail](https://github.com/illuminate/mail) in Laravel one. In order to use single configuration
for the mailer transport you can use `\Yii2tech\Illuminate\Yii\Mail\SwiftMailer` component. It will pickup `\Swift_Mailer`
instance with its transport for Yii mailer from the Laravel one. Application configuration example:

```php
<?php

return [
    'components' => [
        'mailer' => Yii2tech\Illuminate\Yii\Mail\SwiftMailer::class,
        // ...
    ],
    // ...
];
```
