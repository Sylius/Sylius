Customizing Flashes
===================

Why would you customize a flash?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to change any of the flash messages defined in Sylius in any desired language.

For example:

* change the content of a flash when you add resource in the admin
* change the content of a flash when you register in the shop

and many other places where you can customize the text content of the default flashes.

How to customize a flash message?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    You can browse the full implementation of these examples on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/18>`_

In order to customize a resource flash in your project:

**1.** Create the ``translations\flashes.en.yaml`` for english contents of your flashes.

.. note::

    You can create different files for different locales (languages). For example ``flashes.pl.yaml`` should hold only polish flashes,
    as they will be visible when the current locale is ``PL``. Check :doc:`Locales </book/configuration/locales>` docs for more information.

**2.** In this file configure the desired flash key and give it a translation.

If you would like to change the flash message while updating a Taxon, you will need to configure the flash under
the ``sylius.taxon.update`` key:

.. code-block:: yaml

    sylius:
        taxon:
            update: This category has been successfully edited.

Before

.. image:: ../_images/flash_before_customization.png
    :align: center

After

.. image:: ../_images/flash_after_customization.png
    :align: center

.. include:: /customization/plugins.rst.inc
