![Sylius](https://dl.dropboxusercontent.com/u/46579820/sylius-logo.jpg)

[![Gitter chat](https://badges.gitter.im/Sylius/Sylius.png)](https://gitter.im/Sylius/Sylius)
[![License](https://img.shields.io/packagist/l/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Version](https://img.shields.io/packagist/v/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Build status...](https://img.shields.io/travis/Sylius/Sylius/master.svg)](http://travis-ci.org/Sylius/Sylius)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/Sylius.svg)](https://scrutinizer-ci.com/g/Sylius/Sylius/)
[![HHVM Status](https://img.shields.io/hhvm/Sylius/Sylius.svg)](http://hhvm.h4cc.de/package/sylius/sylius)
[![Dependency Status](https://www.versioneye.com/php/sylius:sylius/0.13.0/badge.svg)](https://www.versioneye.com/php/sylius:sylius/0.13.0)

Sylius is an open source e-commerce solution for **PHP**, based on the [**Symfony2**](http://symfony.com) framework.

Ultimate goal of the project is to create a webshop engine, which is user-friendly, *loved* by developers and has a helpful community.

Sylius is constructed from fully decoupled components (bundles in Symfony2 glossary), which means that every feature (products catalog, shipping engine, promotions system...) can be used in any other application. 

We're using full-stack BDD methodology, with [phpspec](http://phpspec.net) and [Behat](http://behat.org).

Documentation
-------------

Documentation is available at [docs.sylius.org](http://docs.sylius.org).

Quick Installation
------------------

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project sylius/sylius:v0.14.0
$ cd sylius
$ php app/console sylius:install
```

The install script will give you the option to run fixtures that make testing and development phases much easier.

[Behat](http://behat.org) scenarios
-----------------------------------

You need to copy Behat default configuration file and enter your specific ``base_url``
option there:

```bash
$ cp behat.yml.dist behat.yml
$ vi behat.yml
```

Then download [Selenium Server](http://seleniumhq.org/download/), and run it:

```bash
$ java -jar selenium-server-standalone-2.41.0.jar
```

Then setup your test database:

```bash
$ php app/console doctrine:database:create --env=test
$ php app/console doctrine:schema:create --env=test
```

You can run Behat using the following commands:

```bash
$ bin/behat # In order to run tests which don't need JS support
$ bin/behat -p javascript # In order to run tests which need JS support
```

Troubleshooting
---------------

If something goes wrong, errors & exceptions are logged at the application level:

```bash
$ tail -f app/logs/prod.log
$ tail -f app/logs/dev.log
```

If you are using the supplied Vagrant development environment, please see the related [Troubleshooting guide](vagrant/README.md#Troubleshooting) for more information.

Contributing
------------

[This page](http://docs.sylius.org/en/latest/contributing/index.html) contains all the information about contributing to Sylius.

Sylius on Twitter
-----------------

If you want to keep up with the updates, [follow the official Sylius account on Twitter](http://twitter.com/Sylius).

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
