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


**Sylius** can be installed using two different approaches, depending on your use case.

Install to Contribute
---------------------

To install Sylius main application from our main repository and contribute, run the following command:

.. code-block:: bash

    $ composer create-project -s dev sylius/sylius

This will create a new sylius project in the ``sylius`` directory. When all the
dependencies are installed, you'll be asked to fill the ``parameters.yml``
file via an interactive script. Please follow the steps. If you hit enter, the default values will be loaded.

.. code-block:: bash

    Creating the "app/config/parameters.yml" file
    Some parameters are missing. Please provide them.
    database_driver (pdo_mysql): # - provide a database driver that you are willing to use
    database_host (127.0.0.1):
    database_port (null):
    database_name (sylius): # - you should rename the database to for instance `my_custom_application_name`
    database_user (root): # - provide the database user and password
    database_password (null): 1234
    mailer_transport (smtp): # - if you will be testing e-mails please provide here your test account data, use `gmail` as transport for example.
    mailer_host (127.0.0.1):
    mailer_user (null): # - your test email
    mailer_password (null): # - and password
    secret (EDITME):
    locale (en_US):
    currency (USD):
    wkhtmltopdf.bin_path (/usr/bin/wkhtmltopdf):
    wkhtmltoimage.bin_path (/usr/bin/wkhtmltoimage):

After everything is in place, run the following commands:

.. code-block:: bash

    $ cd sylius # Move to the newly created directory
    $ php app/console sylius:install

The ``sylius:install`` command actually runs several other commands, which will ask you some questions and check if everything is setup to run Sylius properly.

This package contains our main Sylius development repository, with all the components and bundles in the ``src/`` folder.

For the contributing process questions, please refer to the `Contributing Guide <http://docs.sylius.org/en/latest/contributing/index.html>`_.

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

    $ cd acme # Move to the newly created directory
    $ php app/console sylius:install

This package has the whole ``sylius/sylius`` package in vendors, so you can easily update it and focus on your custom development.

Accessing the Shop
------------------

In order to see the shop, access the ``web/app_dev.php`` file via your web
browser.

.. tip::

    We strongly recommend using the Symfony built-in web server by running the
    ``php app/console server:start 127.0.0.1:8000``
    command and then accessing ``http://127.0.0.1:8000`` in your web browser.

.. note::

    The localhost's 8000 port may be already occupied by some other process.
    If so you should try other ports, like for instance:
    ``php app/console server:start 127.0.0.1:8081``
    Want to know more about using a built-in server, see `here <http://symfony.com/doc/current/cookbook/web_server/built_in.html>`_.

You can log in as an administrator, with the credentials you have provided during the installation process.
Since now you can play with your clean Sylius installation.

Accessing the Administration Panel
----------------------------------

In order to see a fully functional Administration you will need to install `Gulp`_.

**Sylius** already has a ``gulpfile.js``, therefore you just need to get Gulp using `Node.js`_.

Having Node.js installed git to your project directory and run:

.. code-block:: bash

    $ npm install

And now you can use gulp for installing views, by just running a simple command:

.. code-block:: bash

    $ gulp

After you've run the ``gulp`` command please have a look at the ``/admin`` url, where you will find the administration panel.

.. _Gulp: http://gulpjs.com/
.. _Node.js: https://nodejs.org/en/download/
.. _Composer: http://packagist.org
.. _`Composer installed globally`: http://getcomposer.org/doc/00-intro.md#globally
