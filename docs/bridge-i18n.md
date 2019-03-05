Internationalization
====================

In case you have a multilingual project, you will have to transfer static text translations from Yii into Laravel.
These two frameworks have quite a different approaches for translations storage and retrieval. In particular they have
different syntax for message placeholders: while Yii wraps placeholder name into brackets ('{}'), Laravel prefixes them
with colon (':'). Why transferring translations you will have to adjust content of those ones with placeholders accordingly.

In order to make transfer easier you can use [[\Yii2tech\Illuminate\Yii\I18n\I18n]] for internationalization Yii component.
It allows use Laravel translator as a message source for particular translation categories. Application configuration example:

```php
<?php

return [
    'components' => [
        'i18n' => [
            'class' => Yii2tech\Illuminate\Yii\I18n\I18n::class,
            'illuminateCategories' => [ // categories, which messages will be picked from Laravel translator
                'auth',
                'validation',
                // ...
            ],
        ],
        // ...
    ],
    // ...
];
```

Categories, which translations should be passed to Laravel, are determined via [[\Yii2tech\Illuminate\Yii\I18n\I18n::$illuminateCategories]].
Actual translation key will be composed by concatenation of category, dot symbol ('.') and the message.
E.g. Yii translation call `Yii::t('category', 'message')` equals to `__('category.message')`.

In addition, [[\Yii2tech\Illuminate\Yii\I18n\I18n]] provides Laravel-like placeholders (the ones marked by ':' symbol)
replacement for messages retrieved by Yii. Thus following example will be parsed correctly:

```php
<?php

echo Yii::t('blog', '{appName} Blog', ['appName' => 'Yii Project']); // outputs 'Yii Project Blog'
echo Yii::t('blog', ':appName Blog', ['appName' => 'Laravel Project']); // outputs 'Laravel Project Blog'
echo Yii::t('blog', 'Migrating from {from} to :to', ['from' => 'Yii', 'to' => 'Laravel']); // outputs 'Migrating from Yii to Laravel'
```
