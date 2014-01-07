SyliusShippingBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusShippingBundle.png?branch=master)](http://travis-ci.org/Sylius/SyliusShippingBundle)
====================

Shipping component for [**Symfony2**](http://symfony.com) ecommerce applications.
It provides architecture for shipment management system.

It includes flexible calculators engine, which already contains default
calculators. Weight based, item count based and others...

You can also easily define your own pricing by implementing service with
simple interface.

Customizable rules system allows you to define any type of checks, so you can
display right shipping methods to the customer.

Sylius
------

**Sylius** - Modern ecommerce for Symfony2. Visit [Sylius.org](http://sylius.org).

[phpspec](http://phpspec.net) examples
--------------------------------------

```bash
$ composer install
$ bin/phpspec run -fpretty
```

Documentation
-------------

Documentation is available on [**docs.sylius.org**](http://docs.sylius.org/en/latest/bundles/SyliusShippingBundle/index.html).

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

Questions? Feel free to ask on [users mailing list](http://groups.google.com/group/sylius).

### Developers

To contribute and develop this bundle, use the [developers mailing list](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/Sylius).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusShippingBundle/issues).
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

MIT License
-----------

License can be found [here](https://github.com/Sylius/SyliusShippingBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusShippingBundle/contributors).
