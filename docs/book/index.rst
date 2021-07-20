The Book
========

The Developer's guide to leveraging the flexibility of Sylius. Here you will find all the concepts used in Sylius.
The Books helps to understand how Sylius works.

Introduction
------------

Introduction aims to describe the philosophy of Sylius. It will also teach you about environments before you start installing it.

.. toctree::
    :hidden:

    introduction/index

.. include:: /book/introduction/map.rst.inc

Installation
------------

The installation chapter is of course a comprehensive guide to installing Sylius on your machine, but it also provides
a general instruction on upgrading Sylius in your project.

.. toctree::
    :hidden:

    installation/index

.. include:: /book/installation/map.rst.inc

Architecture
------------

The key to understanding principles of Sylius internal organization. Here you will learn about the Resource layer,
state machines, events and general non e-commerce concepts adopted in the platform, like E-mails or Fixtures.

.. toctree::
    :hidden:

    architecture/index

.. include:: /book/architecture/map.rst.inc

Configuration
-------------

Having knowledge about basics of our architecture we will introduce the three most important concepts - Channels, Locales and Currencies.
These things have to be configured before you will have a Sylius application up and running.

.. toctree::
    :hidden:

    configuration/index

.. include:: /book/configuration/map.rst.inc

Customers
---------

This chapter will tell you more about the way Sylius handles users, customers and admins.
There is also a subchapter dedicated to addresses of your customers.

.. toctree::
    :hidden:

    customers/index

.. include:: /book/customers/map.rst.inc

Products
--------

This is a guide to understanding products handling in Sylius together with surrounding concepts. Read about
Associations, Reviews, Attributes, Taxons etc.

.. toctree::
    :hidden:

    products/index

.. include:: /book/products/map.rst.inc

Carts & Orders
--------------

In this chapter you will learn everything you need to know about orders in Sylius.
This concept comes together with a few additional ones, like promotions, payments, shipments or checkout in general.

You should also have a look here if you are looking for Cart, which is in Sylius an Order in the ``cart`` state.

.. toctree::
    :hidden:

    orders/index

.. include:: /book/orders/map.rst.inc

API
---

.. warning::

    The new, unified Sylius API is still under development, that's why the whole ``ApiBundle`` is tagged with ``@experimental``.
    This means that all code from ``ApiBundle`` is excluded from :doc:`Backward Compatibility Promise </book/organization/backward-compatibility-promise>`.

This chapter will explain to you how to start with our new API, show concepts used in it, and you will inform you why we have decided to rebuild entire api from scratch.
To use this API remember to generate JWT token. For more information, please visit `jwt package documentation <https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#generate-the-ssh-keys/>`_

This part of the documentation is about the currently developed unified API for the Sylius platform.

.. toctree::
    :hidden:

    api/index

.. include:: /book/api/map.rst.inc

Themes
------

Here you will learn basics about the Theming concept of Sylius. How to change the theme of your shop? keep reading!

.. toctree::
    :hidden:

    themes/index

.. include:: /book/themes/map.rst.inc

.. rst-class:: plus-doc

Sylius Plus
-----------

`Sylius Plus <https://sylius.com/plus/>`_, which is a licensed edition of Sylius, gives you all the power of Open Source and much more.
It comes with a set of enterprise-grade features and technical support from its creators.
As the state-of-the-art eCommerce platform, it reduces risks and increases your ROI.

Documentation sections of The Book referring to Sylius Plus features are:

.. toctree::
    :maxdepth: 1

    installation/sylius_plus_installation
    architecture/emails
    configuration/channels
    customers/admin_user
    customers/customer_pools
    orders/shipments
    orders/returns
    products/multi_source_inventory
    loyalty/loyalty_rule


.. image:: ../_images/sylius_plus/banner.png
    :align: center
    :target: https://sylius.com/plus/?utm_source=docs

Sylius Plugins
--------------

The collection of Sylius Plugins and basic introduction to the concept of plugins.

.. toctree::
    :hidden:

    plugins/index

.. include:: /book/plugins/map.rst.inc

Organization
------------

This chapter describes the rules and processes we use to organize our work.

.. toctree::
    :hidden:

    organization/index

.. include:: /book/organization/map.rst.inc

Support
-------

How to get support for Sylius?

.. toctree::
   :hidden:

   support/index

.. include:: /book/support/map.rst.inc

Contributing
------------

Guides you how to contribute to Sylius.

.. toctree::
   :hidden:

   contributing/index

.. include:: /book/contributing/map.rst.inc
