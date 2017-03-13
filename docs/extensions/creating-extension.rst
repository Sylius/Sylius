How to create an extension for Sylius?
======================================

Sylius extension is nothing more but a regular Symfony bundle adding custom behaviour to the default Sylius application.

The best way to create your own extension is to use `Sylius bundle skeleton <https://github.com/Sylius/BundleSkeleton>`_,
which has built-in infrastructure for designing and testing using `Behat`_.

1. Create project using Composer.
---------------------------------

.. code-block:: bash

    $ composer create-project sylius/bundle-skeleton SyliusExtensionPath

.. note::

    The extension can be created anywhere, not only inside Sylius application, because it already has the test environment inside.

2. Get familiar with basic extension design.
--------------------------------------------

The skeleton comes with simple application that greets a customer. There are feature scenarios in ``features`` directory;
exemplary bundle with a controller, a template and a routing configuration in ``src``;
and the testing infrastructure in ``tests``.

.. note::

    The ``tests/Application`` directory contains a sample Symfony application used to test your extension.

3. Remove boilerplate files and rename your bundle.
---------------------------------------------------

In most cases you don't want your Sylius extension to greet customer like it is now, so feel free to remove unnecessary
controllers, assets and features. You will also want to change the extension's namespace from ``Acme\ExampleBundle`` to a
more meaningful one. Keep in mind that these changes also need to be done in ``tests/Application`` and ``composer.json``.

4. Implement your awesome features.
-----------------------------------

Looking at existing Sylius extensions like `Lakion\SyliusCmsBundle <https://github.com/Lakion/SyliusCmsBundle>`_
or `Lakion\SyliusElasticSearchBundle <https://github.com/Lakion/SyliusElasticSearchBundle>`_ is
a great way to start developing your own extensions.

Feel free to use `BDD <https://www.agilealliance.org/glossary/bdd/>`_ with `Behat`_, `phpspec`_ and `PhpUnit`_
to ensure your extension's extraordinary quality.

.. tip::

    For the extensions, the suggested way of modifying Sylius is using :doc:`the Customization Guide </customization/index>`.
    There you will find a lot of help while trying to modify templates, state machines, controllers and many, many more.

.. _`Behat`: http://behat.org/en/latest/
.. _`phpspec`: http://www.phpspec.net/en/stable/
.. _`PHPUnit`: https://phpunit.de/
