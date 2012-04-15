SyliusCartBundle
================

Flexible cart engine for Symfony2. Allows you to add any object to cart, customize the structure of it and much more.  
Considered as base for building solution that fits your exact needs.

Features
--------

### Implemented

* Base support for many different persistence layers. Currently only Doctrine ORM driver is implemented.
* Cart item is simple model and you can make it look whatever you like.
* Sensible default model for cart.
* Flexible item resolver that allows you to build and add items the way you prefer.
* Default cart operator implementation handles most basic cart scenario and can be easily extended.
* Common actions like adding/removing items, updating, clearing and viewing the cart are implemented in controllers shipped with the bundle.
* Flushing expired cards
* Thanks to awesome [Symfony2](http://symfony.com) everything is configurable and extensible.
* Unit tested. [![Build status...](https://secure.travis-ci.org/Sylius/SyliusCartBundle.png)](http://travis-ci.org/Sylius/SyliusCartBundle)

### Planned or in progress

* Easy "hybriding", via cartable item loaders, so if you store products and carts in two different persistence layer, you can easily map them to cart.
* Optional "persistent cart" feature, which assigns cart to Symfony Security user and keeps it even when user is not on the website.
* Doctrine MongoDB ODM driver.
* Doctrine CouchDB ODM driver. `*`
* Propel driver. `*`
* 
`*` - wishlist, would love to see a contribution.

Sylius
------

**Sylius** is simple but **end-user and developer friendly** webshop engine built on top of Symfony2.

Please visit [Sylius.org](http://sylius.org) for more details.

Testing and build status
------------------------

This bundle uses [travis-ci.org](http://travis-ci.org/Sylius/SyliusCartBundle) for CI.
[![Build status...](https://secure.travis-ci.org/Sylius/SyliusCartBundle.png)](http://travis-ci.org/Sylius/SyliusCartBundle)

Before running tests, load the dependencies using [Composer](http://packagist.org).

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install --dev
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

Documentation is available on [Sylius.org](http://sylius.org/docs/bundles/SyliusCartBundle.html).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://sylius.org/docs/contributing/index.html).

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

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusCartBundle/issues).
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

License can be found [here](https://github.com/Sylius/SyliusCartBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusCartBundle/contributors).
