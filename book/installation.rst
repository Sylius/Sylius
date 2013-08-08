.. index::
   single: Installation

Installation
============

Sylius main application can serve as end-user app, as well as a foundation for your custom e-commerce application.
The installation method depends on the way you'll to use the software.

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Sylius Standard Edition
-----------------------

Sylius SE is a preconfigured Symfony2 application, using all the bundles together with the Core and Web Interface.
It also contains a sample **AcmeShopBundle**, which can serve as an example of customization.

To start a new project on Sylius, you have to create a new ``sylius-standard`` project.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius-standard:0.2.*@dev

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar create-project -s dev sylius/sylius-standard:0.1.*@dev

When all the dependencies are installed, you'll be asked to fill the ``parameters.yml`` file via interactive script.
Please follow the guide and when everything is in place, finally run the following commands.

.. code-block:: bash

    $ cd sylius-standard
    $ app/console sylius:install

Sylius application
------------------

If you want to simply try out or contribute to Sylius, we recommend installing the ``sylius/sylius`` package, which contains the Behat features, as well as **SyliusCoreBundle** and **SyliusWebBundle** inside ``/src`` directory.

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius:0.2.*@dev

Just like with the Standard Edition, you're going to be asked to fill the ``parameters.yml`` file.

.. code-block:: bash

    $ cd sylius
    $ app/console sylius:install

Accessing the shop
------------------

In order to see the shop, access the ``web/app_dev.php`` via your web browser.
