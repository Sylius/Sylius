How to create a plugin for Sylius?
==================================

Sylius plugin is nothing more but a regular Symfony bundle adding custom behaviour to the default Sylius application.

The best way to create your own plugin is to use `Sylius plugin skeleton <https://github.com/Sylius/PluginSkeleton>`_,
which has built-in infrastructure for designing and testing using `Behat`_.

1. Create project using Composer.
---------------------------------

.. code-block:: bash

    $ composer create-project sylius/plugin-skeleton MyPlugin

.. note::

    The plugin can be created anywhere, not only inside Sylius application, because it already has the test environment inside.

2. Get familiar with basic plugin design.
-----------------------------------------

The skeleton comes with simple application that greets a customer. There are feature scenarios in ``features`` directory;
exemplary bundle with a controller, a template and a routing configuration in ``src``;
and the testing infrastructure in ``tests``.

.. note::

    The ``tests/Application`` directory contains a sample Symfony application used to test your plugin.

3. Remove boilerplate files and rename your bundle.
---------------------------------------------------

In most cases you don't want your Sylius plugin to greet customer like it is now, so feel free to remove unnecessary
controllers, assets and features. You will also want to change the plugin's namespace from ``Acme\ExamplePlugin`` to a
more meaningful one. Keep in mind that these changes also need to be done in ``tests/Application`` and ``composer.json``.

4. Implement your awesome features.
-----------------------------------

Looking at existing Sylius plugins like `Lakion\SyliusCmsBundle <https://github.com/Lakion/SyliusCmsBundle>`_
or `Lakion\SyliusElasticSearchBundle <https://github.com/Lakion/SyliusElasticSearchBundle>`_ is
a great way to start developing your own plugins.

Feel free to use `BDD <https://www.agilealliance.org/glossary/bdd/>`_ with `Behat`_, `phpspec`_ and `PhpUnit`_
to ensure your plugin's extraordinary quality.

.. tip::

    For the plugins, the suggested way of modifying Sylius is using :doc:`the Customization Guide </customization/index>`.
    There you will find a lot of help while trying to modify templates, state machines, controllers and many, many more.

.. _`Behat`: http://behat.org/en/latest/
.. _`phpspec`: http://www.phpspec.net/en/stable/
.. _`PHPUnit`: https://phpunit.de/

5. Naming conventions
---------------------

Besides the way you are creating plugins (based on our skeleton or on your own), there are a few naming conventions that should be followed:

 * Bundle class must have a `Plugin` suffix instead of `Bundle` in its name (e.g. InvoicePlugin).
 * Bundle class must use the `Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait`.
 * The name of the extension in DependencyInjection folder must follow the regular Symfony rules (e.g. InvoiceExtension).
 * The plugin shouldn't have Sylius prefix in its name. `Plugin` suffix in terms of bundles is unique for Sylius at the moment.

The following rules are applied to all bundles which will provide an integration with the whole Sylius platform
(`sylius/sylius` or `sylius/core-bundle` in vendors). Reusable components for the whole Symfony community, which will be based
just on some Sylius bundles should follow regular Symfony conventions.
