SyliusAddressingBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusAddressingBundle.png?branch=master)](http://travis-ci.org/Sylius/SyliusAddressingBundle)
======================

Addresses management is a common task for almost every ecommerce application.  
This bundle, with minimal configuration, provides you sensible models and architecture for addresses, countries and provinces.

It's fully customizable - you can easily add custom fields to your address entity, or split it into several models to handle different address types.
Includes a set of forms that will be sufficient for most popular actions.

Countries can handle multiple provinces (states), and you can easily extend that to more levels by introducing your own models.

Bundle also supports very flexible Zones system, which allows you to group countries and provinces into geographical areas.
Every address can be matched against all defined zones, which is useful for tax or shipping systems.

Sylius
------

Modern ecommerce for Symfony2. Visit [Sylius.org](http://sylius.org).

[phpspec](http://phpspec.net) examples
--------------------------------------

```bash
$ composer install
$ bin/phpspec run -fpretty
```

Documentation
-------------

Documentation is available on [**docs.sylius.org**](http://docs.sylius.org/en/latest/bundles/SyliusAddressingBundle/index.html).

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

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusAddressingBundle/issues).
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

License can be found [here](https://github.com/Sylius/SyliusAddressingBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusAddressingBundle/contributors).
