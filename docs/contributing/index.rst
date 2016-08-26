Contributing
============

.. note::

    This section is based on the great `Symfony documentation <http://symfony.com/doc/current>`_.

Install to Contribute
---------------------

Before you start contributing you need to have your own local environment for editing things.

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

In order to see a fully functional frontend you will need to install its assets.

**Sylius** already has a ``gulpfile.js``, therefore you just need to get `Gulp`_ using `Node.js`_.

Having Node.js installed go to your project directory and run:

.. code-block:: bash

    $ npm install

And now you can use gulp for installing views, by just running a simple command:

.. code-block:: bash

    $ npm run gulp

For the contributing process questions, please refer to the `Contributing Guide <http://docs.sylius.org/en/latest/contributing/index.html>`_ that comes up in the following chapters.

.. _Gulp: http://gulpjs.com/
.. _Node.js: https://nodejs.org/en/download/

.. toctree::
   :hidden:

   code/index
   community/index
   documentation/index

.. include:: /contributing/map.rst.inc
