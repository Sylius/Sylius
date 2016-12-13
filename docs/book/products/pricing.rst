.. index::
   single: Pricing

Pricing
=======

Pricing is a part of Sylius responsible for providing the product prices per channel.

.. note::

    All prices in Sylius are saved in the **base currency** of each channel separately.

Currency per Channel
--------------------

As you already know Sylius operates on :doc:`Channels </book/configuration/channels>`.

Each channel has a **base currency** in which all prices are saved.

.. note::

   Whenever you operate on concepts that have specified values per channel (like `ProductVariant's price`, `Promotion's fixed discount` etc.)

Exchange Rates
--------------

Each currency defined in the system should have an ExchangeRate configured.

**ExchangeRate** is a separate entity that holds a relation between two currencies and specifies their exchange rate.

Exchange rates are used for viewing the *approximate* price in a currency different form the base currency of a channel.

Learn more
----------

* :doc:`Currency - Component Documentation </components/Currency/index>`
* :doc:`Currencies Concept Documentation </book/configuration/currencies>`
