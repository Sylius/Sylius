Basic configuration
===================

The first place you should check out in the Admin panel is the **Configuration** section.
There you can find a bunch of modules used to customize your shop the most basic data.

Channel
-------

The most important one is the **Channels** section. It should consist of one channel already created by you
with the installation command. Channels contain the most basic data about your store, like available locales,
currencies, shop billing data, etc. You can modify the channel's configuration:

.. image:: /_images/getting-started-with-sylius/channel.png

Locale
------

Sylius supports internationalization on many levels - you can easily add new locales to your shop to allow your customers
browsing it in their desired language. As set in the installation command, the only **Locale** available right now
should be **English (United States)**. This will be the base locale of your shop, therefore all of your products or
taxons etc. have to be created with at an english name at least.

.. image:: /_images/getting-started-with-sylius/locale.png

Currency
--------

Each channel operates only on one **Base Currency**, but prices can be shown in multiple **Currencies**, with a ratio between them configured by **Exchange rates**.
For now, the only available currency should be **USD**, which was also created by the ``sylius:install`` command.

.. image:: /_images/getting-started-with-sylius/currency.png

.. note::

    All the previous data was created by the installation command - but you should also add two more things to the store
    configuration to make it work in 100%. It will also be required to have them in the next chapter of this guide.

Country
-------

Most of the shops ship their merchandise to various countries in the world. To configure which countries will be available
as shipping destinations in your store, you should add some countries in the **Countries** section.

Adding a country:

.. image:: /_images/getting-started-with-sylius/country-creation.png

Added country displayed on the index page:

.. image:: /_images/getting-started-with-sylius/country-index.png

Zone
----

The last configuration step is creating a zone. They are used for various reasons, like shipping and taxing operations,
and can consist of countries, provinces or other zones.

.. image:: /_images/getting-started-with-sylius/zones-types.png
    :scale: 55%
    :align: center

|

Let's create one, basic zone named *United States* for the only country in the system (also *United States*).
This way the basic shop configuration is done!

.. image:: /_images/getting-started-with-sylius/zone-creation.png

Learn more
##########

* :doc:`Channels </book/configuration/channels>`
* :doc:`Currencies </book/configuration/currencies>`
* :doc:`Pricing </book/products/pricing>`
* :doc:`Locales </book/configuration/locales>`
