.. index::
   single: Fixtures

Fixtures
========

Fixtures are used mainly for testing, but also for having your shop in a certain state, having defined data
- they ensure that there is a fixed environment in which your application is working.

.. note::

   The way Fixtures are designed in Sylius is well described in the `FixturesBundle documentation <https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md>`_.

What are the available fixtures in Sylius?
------------------------------------------

To check what fixtures are defined in Sylius run:

.. code-block:: bash

   php bin/console sylius:fixtures:list

How to load Sylius fixtures?
----------------------------

The recommended way to load the predefined set of Sylius fixtures is here:

.. code-block:: bash

   php bin/console sylius:fixtures:load

What data is loaded by fixtures in Sylius?
------------------------------------------

All files that serve for loading fixtures of Sylius are placed in the ``Sylius/Bundle/CoreBundle/Fixture/*`` directory.

And the specified data for fixtures is stored in the
`Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml>`_ file.

Available configuration options
-------------------------------

locale
^^^^^^

+---------------------+-----------------------------------------------------------------------------------------------------+
| Configuration key   | Function                                                                                            |
+=====================+=====================================================================================================+
| load_default_locale | Determine if default shop locale (defined as `%locale%`) parameter will be loaded. True by default. |
+---------------------+-----------------------------------------------------------------------------------------------------+
| locales             | Array of locale codes, which will be loaded. Empty by default.                                      |
+---------------------+-----------------------------------------------------------------------------------------------------+

Learn more
----------

* `FixturesBundle documentation <https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md>`_
