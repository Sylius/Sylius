Setup
=====

So you want to try creating an online shop with Sylius? Great! The first step is the most important one, so let's start
with the Sylius project installation via Composer.

Before installation
-------------------

Before creating your Sylius application, you must:

* Install PHP 7.3 or higher, with the following extensions (which are preinstalled in most cases): `gd`_, `exif`_, `fileinfo`_, `intl`_;
* Install MySQL 5.7, 8.0 or higher;
* Install `Composer`_, a PHP package manager;
* Install `Yarn`_, a JavaScript package manager.

.. note::

    While any database supported by `Doctrine DBAL`_ might be used, Sylius comes preconfigured to support MySQL
    and provides database migration only for MySQL.

Project setup
-------------

To create your new project on your local machine, run the command below. It will create a ``MyFirstShop`` directory
with a brand new Sylius application inside.

.. code-block:: bash

    composer create-project sylius/sylius-standard:^1.7 MyFirstShop
    cd MyFirstShop

Sylius uses environment variables to configure credentials to the database and SMTP server. The default values
are defined in ``.env`` file in created project. To override them, create ``.env.local`` file with adjusted values.

.. code-block:: bash

    # .env.local
    DATABASE_URL=mysql://username:password@host/my_custom_sylius_database

After correct database credentials are provided, proceed with running Sylius installation command:

.. code-block:: bash

    php bin/console sylius:install

The first two steps check whether your environment fulfills technical requirements and set up the project database.

You will also be asked if you want to have default fixtures loaded into your database.
Say "No", if you want to proceed with this guide or you want to set it up yourself. Say "Yes", if you want to get an
exemplary Sylius application up and running as soon as possible.

.. image:: /book/setup/_images/installation1.png

It's essential to put some attention to the third installation step. There you configure your default administrator account, which
will be later used to access Sylius admin panel.

.. image:: /book/setup/_images/installation2.png

.. warning::

    ``sylius:install`` command ensures that the default **currency** (USD) and the default **locale** (English - US) are set.
    If you want to change these, set the ``locale`` and ``sylius_installer_currency`` parameters in the ``config/services.yaml`` file
    before installation.

Next, run the following commands. They will install JS dependencies and compile Sylius frontend assets using `Gulp`_.

.. code-block:: bash

    yarn install
    yarn build

Running the application
-----------------------

For development purposes, the fastest way get the application up and running is using the `Symfony binary`_. After installing it,
run the following command:

.. code-block:: bash

    symfony serve

Symfony binary will start up a local webserver. Open your browser and go to `http://localhost:8000/admin`, where you can
access your admin panel and begin configuring your shop. If you've decided to load fixtures during the installation, you
can also access the shop itself at `http://localhost:8000/`.

Start Coding!
-------------

With Sylius installation behind you, it's time to :doc:`configure your application </getting-started-with-sylius/basic-configuration>`.

Learn more
----------

.. toctree::
    :maxdepth: 1
    :glob:

    setup/*

.. _`Composer`: https://getcomposer.org/doc/00-intro.md
.. _`Doctrine DBAL`: https://www.doctrine-project.org/projects/doctrine-dbal/en/2.10/reference/platforms.html
.. _`exif`: https://www.php.net/manual/en/book.exif.php
.. _`fileinfo`: https://www.php.net/manual/en/book.fileinfo.php
.. _`gd`: https://www.php.net/manual/en/book.image.php
.. _`Gulp`: http://gulpjs.com/
.. _`intl`: https://www.php.net/manual/en/book.intl.php
.. _`Symfony binary`: https://symfony.com/download
.. _`Yarn`: https://yarnpkg.com/lang/en/
