.. index::
   single: Installation

Installation
============

The Sylius main application can serve as an end-user app, as well as a foundation
for your custom e-commerce application.

.. warning::

    This article assumes you're familiar with `Composer`_, a dependency manager
    for PHP. It also assumes you have `Composer installed globally`_.

.. note::

    If you downloaded the Composer phar archive, you should use
    ``php composer.phar`` where this guide uses ``composer``.

.. note::

    In order to inform you about newest Sylius releases and be aware of shops based on Sylius,
    the Core Team uses an internal statistical service called GUS.
    The only data that is collected and stored in its database are hostname, user agent, locale,
    environment (test, dev or prod), current Sylius version and the date of last contact.
    If you do not want your shop to send requests to GUS, please visit :doc:`this guide </cookbook/configuration/disabling-admin-notifications>`
    for further instructions.

Initiating A New Sylius Project
-------------------------------

To create a new project using Sylius Standard Edition, run this command:

.. code-block:: bash

    $ composer create-project sylius/sylius-standard acme

.. note::

    Make sure to use PHP ^7.1. Using an older PHP version will result in installing an older version of Sylius.

This will create a new Symfony project in ``acme`` directory. When all the
dependencies are installed, you'll be asked to fill the ``parameters.yml``
file via interactive script. Please follow the steps. After everything is in
place, run the following commands:

.. code-block:: bash

    $ cd acme # Move to the newly created directory
    $ php bin/console sylius:install

This package has the whole ``sylius/sylius`` package in vendors, so you can easily update it and focus on your custom development.

.. warning::

    During the ``sylius:install`` command you will be asked to provide important information, but also its execution ensures
    that the default **currency** (USD) and the default **locale** (English - US) are set they can be later on changed in the ``parameters.yml`` file.
    From now on all the prices will be stored in the database in USD as integers, and all the products will have to be added with a base american english name translation.

Installing assets
-----------------

In order to see a fully functional frontend you will need to install its assets.

**Sylius** already has a ``gulpfile.babel.js``, therefore you just need to get `Gulp`_ using `Yarn`_.

.. note::

    We recommend using stable versions (`^1.0.0`) of `Yarn`_.

Having Yarn installed go to your project directory and run:

.. code-block:: bash

    $ yarn install

And now you can use gulp for installing views, by just running a simple command:

.. code-block:: bash

    $ yarn build

Accessing the Shop
------------------

.. tip::

    We strongly recommend using the Symfony built-in web server by running the
    ``php bin/console server:start --docroot=web 127.0.0.1:8000``
    command and then accessing ``http://127.0.0.1:8000`` in your web browser to see the shop.

.. note::

    The localhost's 8000 port may be already occupied by some other process.
    If so you should try other ports, like for instance:
    ``php bin/console server:start --docroot=web 127.0.0.1:8081``
    Want to know more about using a built-in server, see `here <http://symfony.com/doc/current/cookbook/web_server/built_in.html>`_.

You can log in as an administrator, with the credentials you have provided during the installation process.
Since now you can play with your clean Sylius installation.

Accessing the Administration Panel
----------------------------------

.. note::

    Have a look at the ``/admin`` url, where you will find the administration panel.
    Remember that you have to be logged in as an administrator using the credentials provided while installing Sylius.

How to start developing? - Project Structure
--------------------------------------------

After you have successfully gone through the installation process of **Sylius-Standard** you are probably going to start developing within the framework of Sylius.

In the root directory of your project you will find these important subdirectories:

* ``app/config/`` - here you will be adding the yaml configuration files including routing, security, state machines configurations etc.
* ``var/logs/`` - these are the logs of your application
* ``var/cache/`` - this is the cache of you project
* ``src/`` - this is where you will be adding all you custom logic in the ``AppBundle``
* ``web/`` - there you will be placing assets of your project

.. tip::

    As it was mentioned before we are basing on Symfony, that is why we've adopted its approach to architecture. Read more `in the Symfony documentation <http://symfony.com/doc/current/quick_tour/the_architecture.html>`_.
    Read also about the `best practices while structuring your project <http://symfony.com/doc/current/best_practices/creating-the-project.html#structuring-the-application>`_.

Contributing
------------

.. tip::

    If you would like to contribute to Sylius - please go to the :doc:`Contribution Guide </contributing/index>`

.. _Gulp: http://gulpjs.com/
.. _Yarn: https://yarnpkg.com/lang/en/
.. _Composer: http://packagist.org
.. _`Composer installed globally`: http://getcomposer.org/doc/00-intro.md#globally
