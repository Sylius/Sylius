.. index::
   single: Installation

Installation
============

Sylius main application can serve as end-user app, as well as a foundation for your custom e-commerce application.

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar create-project -s dev sylius/sylius

When all the dependencies are installed, you'll be asked to fill the ``parameters.yml`` file via interactive script.
Please follow the guide and when everything is in place, finally run the following commands.

.. code-block:: bash

    $ cd sylius
    $ app/console sylius:install

Accessing the shop
------------------

In order to see the shop, access the ``web/app_dev.php`` via your web browser.
