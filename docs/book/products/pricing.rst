.. index::
   single: Pricing

Pricing
=======

Pricing is a part of Sylius responsible for providing the product prices per channel.

.. note::

    All prices in Sylius are saved in the **base currency** of each channel separately.

Price and Original Price
------------------------

Price - this is the current price of the product variant displayed in the catalog. It can be modified explicitly by i.e. catalog promotions.
Original price - this is the price of the product variant it is displayed as crossed-out in the catalog. It is used as the base for current price calculations. If this value is not defined, Catalog Promotion logic will copy value from Price to Original Price.

Minimum Price
-------------

Minimum Price is the price below which, any promotion can't decrease price anymore.
It works with Catalog Promotion (it is minimum price in the Product Catalog) and with Cart Promotion (it is a minimum price of product unit in cart).
For example if product should have 5 different promotions, but after third product's price will be below minimum price,
third promotion will decrease price only to minimum price, rest of promotions will not be applied at all.

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

Exchange rates are used for viewing the *approximate* price in a currency different from the base currency of a channel.

Learn more
----------

* :doc:`Currency - Component Documentation </components_and_bundles/components/Currency/index>`
* :doc:`Currencies Concept Documentation </book/configuration/currencies>`
