Coding Standards
================

You can check your code for Sylius coding standard by running the following command:

.. code-block:: bash

    vendor/bin/ecs check src tests

Some of the violations can be automatically fixed by running the same command with ``--fix`` suffix like:

.. code-block:: bash

    vendor/bin/ecs check src tests --fix

.. note::

    Most of Sylius coding standard checks are extracted to `SyliusLabs/CodingStandard`_ package so that
    reusing them in your own projects or Sylius plugins is effortless. Too learn about details, take a look
    at its README.

.. _`SyliusLabs/CodingStandard`: https://github.com/SyliusLabs/CodingStandard
