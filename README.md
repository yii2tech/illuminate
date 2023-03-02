<p align="center">
    <a href="https://github.com/yii2tech" target="_blank">
        <img src="https://avatars2.githubusercontent.com/u/12951949" height="100px">
    </a>
    <h1 align="center">Yii2 to Laravel Migration Package</h1>
    <br>
</p>

This extension allows running Yii2 and Laravel applications simultaneously at the same project,
facilitating graceful migration from Yii2 to Laravel.

For license information check the [LICENSE](LICENSE.md)-file.

[![Latest Stable Version](https://poser.pugx.org/yii2tech/illuminate/v/stable.png)](https://packagist.org/packages/yii2tech/illuminate)
[![Total Downloads](https://poser.pugx.org/yii2tech/illuminate/downloads.png)](https://packagist.org/packages/yii2tech/illuminate)
[![Build Status](https://github.com/yii2tech/illuminate/workflows/build/badge.svg)](https://github.com/yii2tech/illuminate/actions)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist yii2tech/illuminate
```

or add

```json
"yii2tech/illuminate": "*"
```

to the require section of your composer.json.


Why switch from Yii2 to Laravel?
--------------------------------

It is sad to admit, but Yii is outdated technology, which does not keep up with the modern trends.
The core team stick to the BC-keep policy too much since 2.0 release, which make Yii2 lacking of many modern approaches
and features.
While it is common requirement of the modern web project to provide "single page application" based on modern
JS frameworks like ReactJS, EmberJS, VueJS and so on, Yii keeps enforcing  JQuery, facilitating its usage and requiring
its installation.
The BC breaking changes, which are supposed to change the situation, like accepting PSR standards for caching and logging,
separating JQuery from the Yii core and so on, are frozen till the future 3.0 release, which can not be expected in any
near future.

Even when Yii 3.0 will be released, it will hold many BC breaking changes and totally different architecture concept,
regarding of DI and Service Locator usage. This will make migration from Yii 2.x to Yii 3.0 to be the matter of entire
project rewrite, as it already was for migration from Yii 1.x to Yii 2.0. If this is inevitable fate of your project,
why not start code migration now, choosing more reliable technology as its target?
Laravel is most popular PHP framework with solid commercial background and large community. Choosing it will likely bring
good foundation for your project in the long term.

**Heads up!** Whether to switch from one technology to another or not - is **your own** choice. You take the responsibility
for this decision, and you will have to deal with it consequences. Do not blame anyone else for the troubles and obstacles
you will have to face on the chosen path.


Usage
-----

Migration of existing project from one PHP framework to another can not be done by single day. Most likely you have spent
several months or even years creating your current codebase, and its update will also take much time. 

This extension allows running Yii2 and Laravel applications simultaneously at the same project, allowing resolving of
incoming HTTP requests by one of these applications depending on, which one has a matching route defined for it. 
This means all URL routes defined in Yii application will continue to function, while new ones may be resolved by
Laravel. This facilitates graceful migration from one framework to another, allowing progressive transfer of the
URL routes handling (e.g. controllers) from Yii2 to Laravel.

**Heads up!** This package provides tools and libraries helping project migration, however, do not expect it somehow
magically do all the job for you. The package helps solving basic problems and supports the quick start for the process,
but most of the toil will lay on your shoulders. Be ready for it.


Documentation
-------------

Documentation is at [docs/README.md](docs/README.md).
