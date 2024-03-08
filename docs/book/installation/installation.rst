.. index::
   single: Installation

Installation
============

The Sylius main application can serve as an end-user app, as well as a foundation
for your custom e-commerce application.

To create your Sylius-based application, first make sure you use PHP 8.0 or higher
and have `Composer`_ installed.

Initiating A New Sylius Project
-------------------------------

To begin creating your new project, run this command:

.. code-block:: bash

    composer create-project sylius/sylius-standard acme

.. note::

    Make sure to use PHP ^8.0. Using an older PHP version will result in installing an older version of Sylius.

This will create a new Symfony project in the ``acme`` directory. Next, move to the project directory:

.. code-block:: bash

    cd acme

Sylius uses environment variables to configure the connection with database and mailer services.
You can look up the default values in ``.env`` file and customise them by creating ``.env.local`` with variables you want to override.
For example, if you want to change your database name from the default ``sylius_%kernel.environment%`` to ``my_custom_sylius_database``,
the contents of that new file should look like the following snippet:

.. code-block:: text

    DATABASE_URL=mysql://username:password@host/my_custom_sylius_database

.. warning::
    Specific Sylius versions may support various Symfony versions. To make sure the correct Symfony version will be
    installed (Symfony 6.0 for example) use:

    .. code-block:: bash

        composer config extra.symfony.require "^6.0"
        composer update

    Otherwise, you may face the problem of having Symfony components of the wrong version installed.

After everything is in place, run the following command to install Sylius:

.. code-block:: bash

    php bin/console sylius:install

.. warning::

    During the ``sylius:install`` command you will be asked to provide important information, but also its execution ensures
    that the default **currency** (USD) and the default **locale** (English - US) are set.
    They can be changed later, respectively in the "Configuration > Channels" section of the admin and in the ``config/services.yaml`` file. If you want
    to change these before running the installation command, set the ``locale`` and ``sylius_installer_currency`` parameters in the ``config/services.yaml`` file.
    From now on all the prices will be stored in the database in USD as integers, and all the products will have to be added with a base american english name translation.

Configuring Mailer
------------------

In order to send emails you need to configure Mailer Service. Basically there are multiple ways to do it:

* We are recommending to use `Symfony Mailer <https://symfony.com/doc/current/mailer.html>`_ where out of the box, you can deliver emails by configuring the ``MAILER_DSN`` variable in your .env file.
* In Symfony Mailer use the `3rd Party Transports <https://symfony.com/doc/current/mailer.html#using-a-3rd-party-transport>`_
* (deprecated) Use SwiftMailer with this short configuration:

1. **Create an account on a mailing service.**
2. **In your** ``.env`` **file modify/add the** ``MAILER_URL`` **variable.**

.. code-block:: text

    MAILER_URL=gmail://username:password@local

.. note::

    Email delivery is disabled for test, dev and staging environments by default. The prod environment has delivery turned on.

You can learn more about configuring mailer service in :doc:`How to configure mailer? </cookbook/emails/mailer>`

Installing assets
-----------------

In order to see a fully functional frontend you will need to install its assets.

**Sylius** uses `Webpack`_ to build frontend assets using `Yarn`_ as a JavaScript package manager.

.. note::
    If you want to read more, you can read a :doc:`chapter of our Book devoted to the Sylius' frontend </book/frontend/index>`.

Having Yarn installed, go to your project directory to install the dependencies:

.. code-block:: bash

    yarn install

Then build the frontend assets by running:

.. code-block:: bash

    yarn build

Accessing the Shop
------------------

We strongly recommend using the Symfony Local Web Server by running the ``symfony server:start``
command and then accessing ``https://127.0.0.1:8000`` in your web browser to see the shop.

.. note::
    Get to know more about using Symfony Local Web Server `in the Symfony server documentation <https://symfony.com/doc/current/setup/symfony_server.html>`_.
    If you are using a built-in server check `here <https://symfony.com/doc/current/cookbook/web_server/built_in.html>`_.

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

    As it was mentioned before we are basing on Symfony, that is why we've adopted its approach to architecture. Read more `in the Symfony documentation <https://symfony.com/doc/current/quick_tour/the_architecture.html>`_.
    Read also about the `best practices while structuring your project <https://symfony.com/doc/current/best_practices/creating-the-project.html#structuring-the-application>`_.

Running asynchronous tasks
--------------------------

To enable asynchronous tasks (for example for Catalog Promotions), remember about running messenger consumer in a separate process,
use the command: `php bin/console messenger:consume main`

For production environments, we suggest usage of more robust solution like Supervisor,
which will ensure that the process is still running even if some failure will occur.
For more information, please visit `Symfony documentation <https://symfony.com/doc/current/messenger.html#supervisor-configuration>`_.

You can learn more about Catalog Promotions :doc:`Here </book/products/catalog_promotions>`

Contributing
------------

If you would like to contribute to Sylius - please go to the :doc:`Contribution Guide </book/contributing/index>`

.. _Gulp: https://gulpjs.com/
.. _Webpack: https://webpack.js.org/
.. _Yarn: https://yarnpkg.com/lang/en/
.. _Composer: https://packagist.org
