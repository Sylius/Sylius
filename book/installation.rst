.. index::
   single: Installation

Installation
============

The Sylius main application can serve as end-user app, as well as a foundation
for your custom e-commerce application.

This article assumes you're familiar with `Composer`_, a dependency manager
for PHP. It also assumes you have `Composer installed globally`_.

.. note::

    If you downloaded the Composer phar archive, you should use
    ``php composer.phar`` where this guide uses ``composer``.


It can be installed using two different approaches, depending on your use case.

Install to Contribute
---------------------

To install Sylius main application from our main repository and contribute, run the following command:

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius

This will create a new sylius project in ``sylius``. When all the
dependencies are installed, you'll be asked to fill the ``parameters.yml``
file via interactive script. Please follow the steps. After everything is in
place, run the following commands:

.. code-block:: bash

    # Move to the newly created directory
    $ cd sylius
    $ php app/console sylius:install

This package contains our main Sylius development repository, with all the components and bundles in the ``src/`` folder.

For the contributing process questions, please refer to the ['Contributing Guide <http://docs.sylius.org/en/latest/contributing/index.html>'_].

Bootstrap A New Sylius Project
------------------------------

To create a new project using Sylius Standard Edition, run this command:

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius-standard acme

This will create a new Symfony project in ``acme`` directory. When all the
dependencies are installed, you'll be asked to fill the ``parameters.yml``
file via interactive script. Please follow the steps. After everything is in
place, run the following commands:

.. code-block:: bash

    # Move to the newly created directory
    $ cd acme
    $ php app/console sylius:install

This package has the whole ``sylius/sylius`` package in vendors, so you can easily updated it and focus on your custom development.

Accessing the Shop
------------------

In order to see the shop, access the ``web/app_dev.php`` file via your web
browser.

.. tip::

    If you use PHP 5.4 or higher, you can also use the build-in webserver for
    Symfony. Run the ``php app/console server:run`` command and then access
    ``http://localhost:8000``.

.. _Composer: http://packagist.org
.. _`Composer installed globally`: http://getcomposer.org/doc/00-intro.md#globally
