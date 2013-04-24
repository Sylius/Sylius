SyliusResourceBundle [![Build status...](https://secure.travis-ci.org/Sylius/SyliusResourceBundle.png)](http://travis-ci.org/Sylius/SyliusResourceBundle)
====================

Easy CRUD and persistence for Symfony2 apps.

During our work on Sylius, we noticed a lot of duplicated code across all controllers. We started looking for good solution of the problem.
We're not big fans of administration generators (they're cool, but not for our usecase!) - we wanted something simpler and more flexible.

Another idea was to not limit ourselves to one persistence backend. Initial implementation included custom manager classes, which was quite of overhead, so we decided to simply 
stick with Doctrine Common Persistence interfaces. If you are using Doctrine ORM or any of the ODM's, you're already familiar with those concepts.
Resource bundle relies mainly on `ObjectManager` and `ObjectRepository` interfaces.

The last annoying problem this bundle is trying to solve, is having separate "backend" and "frontend" controllers, or any other duplication for displaying the same resource,
with different presentation (view). We also wanted an easy way to filter some resources from list, sort them or display by id, slug or any other criteria - without having to defining
another super simple action for that purpose.

If these are issues you're struggling with, this bundle may be helpful!

Please note that this bundle **is not admin generator**. It won't create forms, filters and grids for you. It only provides format agnostic controllers as foundation to build on, with some basic sorting and filter mechanisms.

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

Documentation is available on [**docs.sylius.com**](http://docs.sylius.com/en/latest/bundles/SyliusResourceBundle/index.html).

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

For more information on SemVer, please visit [semver.org website](http://semver.org/).  
This versioning method is same for all **Sylius** bundles and applications.

MIT License
-----------

License can be found [here](https://github.com/Sylius/SyliusResourceBundle/blob/master/Resources/meta/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/SyliusResourceBundle/contributors).
