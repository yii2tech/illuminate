What do you need to know
========================

Before start the project migration you should perform some basic steps to be prepared for the things to come.

## PHP

Make sure your Yii2 project is running on PHP >= 7.2. The project should not only be compatible with it, but have
it as a background in any environment, which it can be deployed into, like dev server, stage and production ones.

## Yii

Make sure your project is using latest Yii 2.0.x version and does not use deprecated features like `className()`
method and so on.

## Laravel

You should be familiar with [Laravel Framework](https://laravel.com/docs) before you start. Make sure you
understand at least its basic features and concepts, like DI container, Middleware and so on.

## Array Factory

This package uses [illuminatech/array-factory](https://github.com/illuminatech/array-factory) for internal configuration.
This library provides ability of lazy-style creation of objects from array configuration, using syntax and approach similar
to the one used in Yii. Make sure you have basic understanding of how this library works.
