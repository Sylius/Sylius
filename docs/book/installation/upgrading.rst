.. index::
   single: Upgrading

Upgrading
=========

Sylius is releasing new versions from time to time. Each release is supported with an UPGRADE file, which is meant to help in the upgrading process,
especially for the major versions, which can break the backwards compatibility.

**Update the Sylius library version constraint by modifying the** ``composer.json`` **file:**

.. code-block:: yaml

    {
        ...

        "require": {
            "...": "...",

            "sylius/sylius": "^1.0@beta",

            "...": "...",
        },

        ...
    }

**Then run** ``composer update`` **command:**

.. code-block:: bash

    $ composer update sylius/sylius

If this results in a dependency error, it may mean that other Sylius dependencies also have to be upgraded.
Using this command may help you upgrade Sylius dependencies.

.. code-block:: bash

    $ composer update sylius/sylius --with-dependencies

If this does not help, it is a matter of debugging the conflicting versions and working out how your ``composer.json`` should look after the upgrade.

**Finally to make everything work check the UPGRADE file of Sylius for instructions.**

**One more important thing is running the database migrations:**

.. code-block:: bash

    $ bin/console doctrine:migrations:migrate

.. tip::

    Check if the migrations (more than 1) are in your ``app/migrations`` directory. If not, then replace the contents
    of this directory with the migrations from the ``vendor/sylius/sylius/app/migrations/`` directory.

After fixing the project according to the upgrade and having run the migrations you are done!
