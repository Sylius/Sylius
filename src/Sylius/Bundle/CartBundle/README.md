SyliusCartsBundle
=================

Flexible cart engine for Symfony2. Allows you to add any object to cart, customize the structure of it and much more.  
Considered as base for building solution that fits your exact needs.

[![Build status...](https://secure.travis-ci.org/Sylius/SyliusCartsBundle.png)](http://travis-ci.org/Sylius/SyliusCategorizerBundle)

Sylius
------

**Sylius** is simple but **end-user and developer friendly** webshop engine built on top of Symfony2.

Please visit [Sylius.org](http://sylius.org) for more details.

Testing and build status
------------------------

This bundle uses [travis-ci.org](http://travis-ci.org/Sylius/SyliusCartsBundle) for CI.
[![Build status...](https://secure.travis-ci.org/Sylius/SyliusCartsBundle.png)](http://travis-ci.org/Sylius/SyliusCategorizerBundle)

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

Documentation is available on [Sylius.org](http://sylius.org/docs/bundles/SyliusCartsBundle.html).

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

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusCartsBundle/issues).
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

License can be found [here](https://github.com/Sylius/SyliusCartsBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusCartsBundle/contributors).
