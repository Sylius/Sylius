How to use installer commands?
==============================

Sylius platform ships with the ``sylius:install`` command, which takes care of creating the database, schema, dumping the assets and basic store configuration.

This command actually uses several other commands behind the scenes and each of those is available for you:

Checking system requirements
----------------------------

You can quickly check all your system requirements and possible recommendations by calling the following command:

.. code-block:: bash

    php bin/console sylius:install:check-requirements

Database configuration
----------------------

Sylius can create or even reset the database/schema for you, simply call:

.. code-block:: bash

    php bin/console sylius:install:database

The command will check if your database schema exists. If yes, you may decide to recreate it from scratch, otherwise Sylius will take care of this automatically.
It also allows you to load sample data.
You can review the Sylius Database Schema `here <https://drawsql.app/templates/sylius>`_.

Loading sample data
-------------------

You can load sample data by calling the following command:

.. code-block:: bash

    php bin/console sylius:install:sample-data

Basic store configuration
-------------------------

To configure your store, use this command and answer all questions:

.. code-block:: bash

    php bin/console sylius:install:setup

Installing assets
-----------------

You can reinstall all web assets by simply calling:

.. code-block:: bash

    php bin/console sylius:install:assets

How to load custom fixtures suite?
==================================

If you have your custom fixtures suite, you can load it during install by providing at `fixture-suite` parameter:

.. code-block:: bash

    php bin/console sylius:install --fixture-suite=your_custom_fixtures_suite

Same option also available at `sylius:install:database`, `sylius:install:sample-data` commands.
