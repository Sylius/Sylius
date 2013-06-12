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
$ vi app/config/parameters.yml # And put your values!
$ php app/console doctrine:database:create
$ php app/console doctrine:schema:create
$ php app/console doctrine:fixtures:load # If you want to load sample data.
```

[Behat](http://behat.org) scenarios
-----------------------------------

You need to copy Behat default configuration file and enter your specific ``base_url``
option there.

```bash
$ cp behat.yml.dist behat.yml
$ vi behat.yml
```

Then download [Selenium Server](http://seleniumhq.org/download/), and run it.

```bash
$ java -jar selenium-server-standalone-2.11.0.jar
```
You can run Behat using the following command.

``` bash
$ bin/behat
$ bin/behat -p no-js # If you want to skip the scenarios which require real browser.
```

Troubleshooting
------------
If something goes wrong, errors & exceptions are logged at the application level:
````
tail -f app/logs/prod.log
tail -f app/logs/dev.log
````

Contributing
------------

All informations about contributing to Sylius can be found on [this page](http://docs.sylius.org/en/latest/contributing/index.html).

Sylius on twitter
-----------------

If you want to keep up with the updates, [follow the official Sylius account on twitter](http://twitter.com/Sylius).

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
