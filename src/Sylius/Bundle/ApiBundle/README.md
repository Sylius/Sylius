SyliusApiBundle
================

Unified API for Sylius eCommerce.

Sylius
------

![Sylius](https://demo.sylius.com/assets/shop/img/logo.png)

Sylius is an Open Source eCommerce solution built from decoupled components with powerful API and the highest quality code. [Read more on sylius.com](https://sylius.com).

Documentation
-------------

Documentation is available on [**docs.sylius.com**](https://docs.sylius.com).

Contributing
------------

[This page](https://docs.sylius.com/en/latest/contributing/index.html) contains all the information about contributing to Sylius.

Follow Sylius' Development
--------------------------

If you want to keep up with the updates and latest features, follow us on the following channels:

* [Official Blog](https://sylius.com/blog)
* [Sylius on Twitter](https://twitter.com/Sylius)
* [Sylius on Facebook](https://facebook.com/SyliusEcommerce)

Bug tracking
------------

Sylius uses [GitHub issues](https://github.com/Sylius/Sylius/issues).
If you have found bug, please create an issue.

MIT License
-----------

License can be found [here](https://github.com/Sylius/Sylius/blob/master/LICENSE).

Authors
-------

The bundle was originally created by [Paweł Jędrzejewski](https://pjedrzejewski.com).
See the list of [contributors](https://github.com/Sylius/Sylius/contributors).

Testing
-----------------------

To test locally, run commands:
```bash
(cd src/Sylius/Bundle/ApiBundle && composer install)
(cd src/Sylius/Bundle/ApiBundle/test && bin/console doctrine:database:create -e test)
(cd src/Sylius/Bundle/ApiBundle/test && bin/console doctrine:schema:update --force -e test)
(cd src/Sylius/Bundle/ApiBundle/test && bin/console assets:install public)
(cd src/Sylius/Bundle/ApiBundle/test && APP_ENV=test symfony serve)
```

To run tests:
```bash
(cd src/Sylius/Bundle/ApiBundle && vendor/bin/phpunit)
```
