SyliusSalesBundle
=================

Sales management for Symfony2 e-commerce applications.
Provides sensible default models, forms, controllers and generally architecture for handling orders.
Allows using simple orders, orders with items, create and manage their statuses via web interface.
Whole system is easy extensible thanks to events, clean interfaces and many other extension ports.

**This bundle is compatible only with 2.1.x branch of Symfony2**.

Features
--------

* Base support for many different persistence layers. Currently only Doctrine ORM driver is implemented.
* Provides two kinds of order models, simple single object, and extended order with multiple items handling.
* Includes order status management, both CRUD and setting status of specific order.
* Supports order confirmation out of the box, via mail.
* It uses [Pagerfanta](http://github.com/whiteoctober/Pagerfanta) to paginate over the orders.
* Thanks to awesome [Symfony2](http://symfony.com) everything is configurable and extensible.
* Unit tested. [![Build status...](https://secure.travis-ci.org/Sylius/SyliusSalesBundle.png)](http://travis-ci.org/Sylius/SyliusSalesBundle)

Sylius
------

**Sylius** is simple but **end-user and developer friendly** webshop engine built on top of Symfony2.

Please visit [Sylius.org](http://sylius.org) for more details.

Testing and build status
------------------------

This bundle uses [travis-ci.org](http://travis-ci.org/Sylius/SyliusSalesBundle) for CI.
[![Build status...](https://secure.travis-ci.org/Sylius/SyliusSalesBundle.png)](http://travis-ci.org/Sylius/SyliusSalesBundle)

Before running tests, load the dependencies using [Composer](http://packagist.org).

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install --install-suggests
```

Now you can run the tests by simply using this command.

``` bash
$ phpunit
```

Code examples
-------------

If you want to see working implementation, try out the [Sylius sandbox application](http://github.com/Sylius/Sylius-Sandbox).
It's open sourced github project.

Documentation
-------------

Documentation is available on [readthedocs.org](http://sylius.readthedocs.org/en/latest/bundles/SyliusSalesBundle.html).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://sylius.readthedocs.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

If you are using this bundle and have any questions, feel free to ask on users mailing list.
[Mail](mailto:sylius@googlegroups.com) or [view it](http://groups.google.com/group/sylius).

### Developers

If you want to contribute, and develop this bundle, use the developers mailing list.
[Mail](mailto:sylius-dev@googlegroups.com) or [view it](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/_Sylius)
or [follow me](http://twitter.com/pjedrzejewski).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusSalesBundle/issues).
If you have found bug, please create an issue.

Versioning
----------

Releases will be numbered with the format `major.minor.patch`.

And constructed with the following guidelines.

* Breaking backwards compatibility bumps the major.
* New additions without breaking backwards compatibility bumps the minor.
* Bug fixes and misc changes bump the patch.

For more information on SemVer, please visit [semver.org website](http://semver.org/).

This versioning method is same for all **Sylius** bundles and applications.

License
-------

License can be found [here](https://github.com/Sylius/SyliusSalesBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

<<<<<<< HEAD
The bundle was originally created by [Paweł Jędrzejewski](http://diweb.pl).
=======
The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
>>>>>>> 9f73c0b5520661f0820b2f5b8676bb2124b1a442
See the list of [contributors](https://github.com/Sylius/SyliusSalesBundle/contributors).
