SyliusResourceBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusResourceBundle.png)](http://travis-ci.org/Sylius/SyliusResourceBundle)
====================

While the main Sylius application will probably stick to ORM implementation, we do not want to tie any of the components to a specific
content storage. This bundle allows us to avoid a lot of duplicated logic for each bundle - controllers, managers and repositories.
It also gives us some level of abstraction, which allows using different database layers. Hidden under popular Doctrine persistence interfaces.

Sylius
------

**Sylius**, webshop engine for Symfony2.

Visit [Sylius.org](http://sylius.org).

[phpspec2](http://phpspec.net) Specifications
---------------------------------------------

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install --dev
$ bin/phpspec run
```

Documentation
-------------

Documentation is available on [**docs.sylius.org**](http://docs.sylius.org/en/latest/bundles/SyliusResourceBundle/index.html).

Code examples
-------------

If you want to see working implementation, try out the [Sylius sandbox application](http://github.com/Sylius/Sylius-Sandbox).


Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://sylius.readthedocs.org/en/latest/contributing/index.html).

Mailing lists
-------------

### Users

Questions? Feel free to ask on [users mailing list](http://groups.google.com/group/sylius).

### Developers

To contribute and develop this bundle, use the [developers mailing list](http://groups.google.com/group/sylius-dev).

Sylius twitter account
----------------------

If you want to keep up with updates, [follow the official Sylius account on twitter](http://twitter.com/_Sylius).

Bug tracking
------------

This bundle uses [GitHub issues](https://github.com/Sylius/SyliusResourceBundle/issues).
If you have found bug, please create an issue.

Versioning
----------

Releases will be numbered with the format `major.minor.patch`.

And constructed with the following guidelines.

* Breaking backwards compatibility bumps the major.
* New additions without breaking backwards compatibility bumps the minor.
* Bug fixes and misc changes bump the patch.

For more information on SemVer, please visit [semver.org website](http://semver.org/)...
This versioning method is same for all **Sylius** bundles and applications.

MIT License
-----------

License can be found [here](https://github.com/Sylius/SyliusResourceBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusResourceBundle/contributors).
