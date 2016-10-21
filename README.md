![Sylius](http://demo.sylius.org/assets/shop/img/logo.png)

[![Gitter chat](https://badges.gitter.im/Sylius/Sylius.png)](https://gitter.im/Sylius/Sylius)
[![License](https://img.shields.io/packagist/l/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Version](https://img.shields.io/packagist/v/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Build status on Linux](https://img.shields.io/travis/Sylius/Sylius/master.svg)](http://travis-ci.org/Sylius/Sylius)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/Sylius.svg)](https://scrutinizer-ci.com/g/Sylius/Sylius/)
[![Dependency Status](https://www.versioneye.com/php/sylius:sylius/badge.svg)](https://www.versioneye.com/php/sylius:sylius)
[![Total Downloads](https://poser.pugx.org/sylius/sylius/downloads)](https://packagist.org/packages/sylius/sylius)

Sylius is the first decoupled eCommerce framework based on [**Symfony**](http://symfony.com) and [**Doctrine**](http://doctrine-project.org). 
The highest quality of code, strong testing culture, built-in Agile (BDD) workflow and exceptional flexibility make it the best solution for application tailored to your business requirements. 
Enjoy being an eCommerce Developer again!

We're using full-stack Behavior-Driven-Development, with [phpspec](http://phpspec.net) and [Behat](http://behat.org)

Documentation
-------------

Documentation is available at [docs.sylius.org](http://docs.sylius.org).

Installation
------------

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project -s alpha sylius/sylius-standard app
$ cd app
$ npm install
$ npm run gulp
$ php app/console sylius:install
$ php app/console server:start
$ open http://localhost:8000/
```

Troubleshooting
---------------

If something goes wrong, errors & exceptions are logged at the application level:

```bash
$ tail -f app/logs/prod.log
$ tail -f app/logs/dev.log
```

If you are using the supplied Vagrant development environment, please see the related [Troubleshooting guide](etc/vagrant/README.md#Troubleshooting) for more information.

Contributing
------------

Would like to help us and build the most developer-friendly eCommerce platform? Start from reading our [Contributing Guide](http://docs.sylius.org/en/latest/contributing/index.html)!

Stay Updated
------------

If you want to keep up with the updates, [follow the official Sylius account on Twitter](http://twitter.com/Sylius) and [like us on Facebook](https://www.facebook.com/SyliusEcommerce/).

Bug Tracking
------------

If you want to report a bug or suggest an idea, please use [GitHub issues](https://github.com/Sylius/Sylius/issues).

Community Support
-----------------

Have a question? Join our [Gitter](https://gitter.im/Sylius/Sylius) or post a question on [StackOverflow](http://stackoverflow.com) tagged with "sylius".

MIT License
-----------

Sylius is completely free and released under the [MIT License](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

Sylius was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors from our awesome community](https://github.com/Sylius/Sylius/contributors).
