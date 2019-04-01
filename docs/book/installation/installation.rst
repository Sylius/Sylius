.. index::
   single: Installation

Installation
============

The Sylius main application can serve as an end-user app, as well as a foundation
for your custom e-commerce application.

To create your Sylius-based application, first make sure you use PHP 7.2 or higher
and have `Composer`_ installed.

.. note::

    In order to inform you about newest Sylius releases and be aware of shops based on Sylius,
    the Core Team uses an internal statistical service called GUS.
    The only data that is collected and stored in its database are hostname, user agent, locale,
    environment (test, dev or prod), current Sylius version and the date of last contact.
    If you do not want your shop to send requests to GUS, please visit :doc:`this guide </cookbook/configuration/disabling-admin-notifications>`
    for further instructions.

Initiating A New Sylius Project
-------------------------------

To begin creating your new project, run this command:

.. code-block:: bash

    $ composer create-project sylius/sylius-standard acme

.. note::

    Make sure to use PHP ^7.2. Using an older PHP version will result in installing an older version of Sylius.

This will create a new Symfony project in the ``acme`` directory. Next, move to the project directory:

.. code-block:: bash

    $ cd acme

Sylius uses environment variables to configure the connection with database and mailer services.
You can look up the default values in ``.env`` file and customise them by creating ``.env.local`` with variables you want to override.
For example, if you want to change your database name from the default ``sylius_%kernel.environment`` to ``my_custom_sylius_database``,
the contents of that new file should look like the following snippet:

.. code-block:: text

    DATABASE_URL=mysql://username:password@host/my_custom_sylius_database

After everything is in place, run the following command to install Sylius:

.. code-block:: bash

    $ php bin/console sylius:install

.. warning::

    During the ``sylius:install`` command you will be asked to provide important information, but also its execution ensures
    that the default **currency** (USD) and the default **locale** (English - US) are set.
    They can be changed later, respectively in the "Configuration > Channels" section of the admin and in the ``config/services.yaml`` file.
    From now on all the prices will be stored in the database in USD as integers, and all the products will have to be added with a base american english name translation.

Installing assets
-----------------

In order to see a fully functional frontend you will need to install its assets.

**Sylius** uses `Gulp`_ to build frontend assets using `Yarn`_ as a JavaScript package manager.

Having Yarn installed, go to your project directory to install the dependencies:

.. code-block:: bash

    $ yarn install

Then build the frontend assets by running:

.. code-block:: bash

    $ yarn build

Accessing the Shop
------------------

We strongly recommend using the Symfony built-in web server by running the ``php bin/console server:start``
command and then accessing ``http://127.0.0.1:8000`` in your web browser to see the shop.

.. note::

    The localhost's 8000 port may be already occupied by some other process.
    If that happens, please try using a different port - ``php bin/console server:start 127.0.0.1:8081``.
    Get to know more about using a built-in server `here <http://symfony.com/doc/current/cookbook/web_server/built_in.html>`_.

You can log to the administrator panel located at ``/admin`` with the credentials you have provided during the installation process.

How to start developing? - Project Structure
--------------------------------------------

After you have successfully gone through the installation process of **Sylius-Standard** you are probably going to start developing within the framework of Sylius.

In the root directory of your project you will find these important subdirectories:

* ``config/`` - here you will be adding the yaml configuration files including routing, security, state machines configurations etc.
* ``var/log/`` - these are the logs of your application
* ``var/cache/`` - this is the cache of you project
* ``src/`` - this is where you will be adding all you custom logic in the ``App``
* ``public/`` - there you will be placing assets of your project

.. tip::

    As it was mentioned before we are basing on Symfony, that is why we've adopted its approach to architecture. Read more `in the Symfony documentation <http://symfony.com/doc/current/quick_tour/the_architecture.html>`_.
    Read also about the `best practices while structuring your project <http://symfony.com/doc/current/best_practices/creating-the-project.html#structuring-the-application>`_.

Contributing
------------

If you would like to contribute to Sylius - please go to the :doc:`Contribution Guide </contributing/index>`

.. _Gulp: http://gulpjs.com/
.. _Yarn: https://yarnpkg.com/lang/en/
.. _Composer: http://packagist.org
