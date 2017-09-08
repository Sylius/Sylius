Test configuration source
=========================

**Test** configuration source provides an interface that can be used to add, remove and access themes in test environment.
They are stored in the cache directory and if used with Behat, they are persisted across steps but not across scenarios.

Configuration reference
-----------------------

This source does not have any configuration options. To enable it, use the following configuration:

.. code-block:: yaml

    sylius_theme:
        sources:
            test: ~

Usage
-----

In order to use tests, have a look at ``sylius.theme.test_theme_configuration_manager`` service
(implementing `TestThemeConfigurationManagerInterface`_). You can:

 - add a theme: ``void add(array $configuration)``
 - remove a theme: ``void remove(string $themeName)``
 - remove all themes: ``void clear()``

.. _TestThemeConfigurationManagerInterface: http://api.sylius.org/Sylius/Bundle/ThemeBundle/Configuration/Test/TestThemeConfigurationManagerInterface.html
