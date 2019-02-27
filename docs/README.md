Yii2 to Laravel Migration Package
=================================

This extension allows project migration from Yii2 to Laravel.

The process starts from the stage when Yii2 and Laravel applications are running simultaneously, resolving the
incoming HTTP requests by one of these applications, depending on, which one has a matching route defined for it.
Thus some routes are handled by Yii and others by Laravel. Such project state is called "Two-headed Beast".
Laravel will serve as "host" or "master" application, while legacy Yii will be "resident" or "slave".
Obviously, "Two-headed Beast" is not good in terms of performance, but it is necessary as an existing project will
take much time to full framework migration. This package provides your means for quick creation of such project state,
solving some basic troubles and reducing performance impact caused by it.
Afterwards, your ultimate goal is "chop the legacy head", step by step moving code from Yii application to Laravel one.


Getting Started
---------------

* [What do you need to know](start-prerequisites.md)
* [Installation](start-installation.md)
* [Database](start-database.md)
* [Session and Authentication](start-session-and-authentication.md)


Feature Bridges
---------------

* [Dependency Injection](bridge-di.md)
* [Logging](bridge-logging.md)
* [Caching](bridge-caching.md)
* [Internationalization](bridge-i18n.md)
