.. index::
   single: Fixtures

Fixtures
========

Fixtures are used mainly for testing, but also for having your shop in a certain state, having defined data
- they ensure that there is a fixed environment in which your application is working.

.. note::

   They way Fixtures are designed in Sylius is well described in the :doc:`FixturesBundle documentation </bundles/SyliusFixturesBundle/index>`.

What are the available fixtures in Sylius?
------------------------------------------

To check what fixtures are defined in Sylius run:

.. code-block:: bash

   $ php bin/console sylius:fixtures:list

How to load Sylius fixtures?
----------------------------

The recommended way to load the predefined set of Sylius fixtures is here:

.. code-block:: bash

   $ php bin/console sylius:fixtures:load

What data is loaded by fixtures in Sylius?
------------------------------------------

All files that serve for loading fixtures of Sylius are placed in the ``Sylius/Bundle/CoreBundle/Fixture/*`` directory.

And the specified data for fixtures is stored in the
`Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml>`_ file.

Learn more
----------

* :doc:`FixturesBundle documentation </bundles/SyliusFixturesBundle/index>`
