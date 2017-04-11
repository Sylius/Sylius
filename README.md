![Sylius](http://demo.sylius.org/assets/shop/img/logo.png)

[![License](https://img.shields.io/packagist/l/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Version](https://img.shields.io/packagist/vpre/Sylius/Sylius.svg)](https://packagist.org/packages/sylius/sylius)
[![Build status on Linux](https://img.shields.io/travis/Sylius/Sylius/master.svg)](http://travis-ci.org/Sylius/Sylius)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/Sylius/Sylius.svg)](https://scrutinizer-ci.com/g/Sylius/Sylius/)
[![Total Downloads](https://poser.pugx.org/sylius/sylius/downloads)](https://packagist.org/packages/sylius/sylius)

Sylius is the first decoupled eCommerce framework based on [**Symfony**](http://symfony.com) and [**Doctrine**](http://doctrine-project.org). 
The highest quality of code, strong testing culture, built-in Agile (BDD) workflow and exceptional flexibility make it the best solution for application tailored to your business requirements. 
Enjoy being an eCommerce Developer again!

Powerful REST API allows for easy integrations and creating unique customer experience on any device.

We're using full-stack Behavior-Driven-Development, with [phpspec](http://phpspec.net) and [Behat](http://behat.org)

Documentation
-------------

Documentation is available at [docs.sylius.org](http://docs.sylius.org).

Installation
------------

```bash
$ wget http://getcomposer.org/composer.phar
$ php composer.phar create-project -s beta sylius/sylius-standard project
$ cd project
$ php bin/console sylius:install
$ yarn install
$ yarn run gulp
$ php bin/console server:start
$ open http://localhost:8000/
```

More information on installation can be found [in the documentation](http://docs.sylius.org/en/latest/book/installation/vagrant_installation.html).

To use Vagrant, see [this repository](http://github.com/Sylius/Vagrant) and [this guide](http://docs.sylius.org/en/latest/book/installation/installation.html).

Troubleshooting
---------------

If something goes wrong, errors & exceptions are logged at the application level:

```bash
$ tail -f var/logs/prod.log
$ tail -f var/logs/dev.log
```

If you are using the supplied Vagrant development environment, please see the related [Troubleshooting guide](http://github.com/Sylius/Vagrant/README.md#Troubleshooting) for more information.

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

Have a question? Join our [Slack](https://slackinvite.me/to/sylius-devs) or post it on [StackOverflow](http://stackoverflow.com) tagged with "sylius". You can also join our [group on Facebook](https://www.facebook.com/groups/sylius/)!

MIT License
-----------

Sylius is completely free and released under the [MIT License](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

Sylius was originally created by [Paweł Jędrzejewski](http://pjedrzejewski.com).
See the list of [contributors from our awesome community](https://github.com/Sylius/Sylius/contributors).
