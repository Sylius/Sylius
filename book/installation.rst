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

To create a new project using Sylius, run this command:

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius

This will create a new sylius project in ``sylius``. When all the
dependencies are installed, you'll be asked to fill the ``parameters.yml``
file via interactive script. Please follow the steps. After everything is in
place, run the following commands:

.. code-block:: bash

    # move to the newly created sylius directory
    $ cd sylius
    $ php app/console sylius:install

Accessing the Shop
------------------

In order to see the shop, access the ``web/app_dev.php`` file via your web
browser.

.. tip::

    If you use PHP 5.4 or higher, you can also use the build-in webserver for
    Symfony. Run the ``php app/console server:run`` command and then access
    ``http://localhost:8000``.

.. _Composer: http://packagist.org
.. _`Composer isntalled globally`: http://getcomposer.org/doc/00-intro.md#globally
