.. index::
   single: Installation

Installation
============

There are several ways to install Sylius main app for the standard usage.

Using Git
---------

...

Using Composer
--------------

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

    $ cd sylius-standard
    $ app/console sylius:install
