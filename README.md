Sylius [![Build status...](https://secure.travis-ci.org/Sylius/Sylius.png?branch=master)](http://travis-ci.org/Sylius/Sylius)
======

Sylius is **modern ecommerce solution for PHP**, based on the [**Symfony2**](http://symfony.com) framework.

Installation
------------

``` bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project sylius/sylius -s dev
```

Then configure your project and create database.

``` bash
$ cd sylius
$ vi sylius/config/container/parameters.yml # And put your values!
$ php sylius/console doctrine:database:create
$ php sylius/console doctrine:schema:create
$ php sylius/console doctrine:fixtures:load # If you want to load sample data.
```

[Behat](http://behat.org) scenarios
-----------------------------------

``` bash
$ ./bin/behat
```

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Sylius on twitter
-----------------

If you want to keep up with the updates, [follow the official Sylius account on twitter](http://twitter.com/_Sylius).

Bug tracking
------------

Sylius uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
If you have found bug, please create an issue.

MIT License
-----------

License can be found [here](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

Sylius was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/Sylius/contributors).
