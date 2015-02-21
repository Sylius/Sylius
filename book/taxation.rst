.. index::
   single: Taxation

Taxation
========

Sylius has a very flexible taxation system, which allows you to apply appropriate taxes for different items, billing zones and use custom calculators.

Tax Categories
--------------

In order to process taxes in your store, you need to configure at least one **TaxCategory**, which represents a specific type of merchandise.
If all your items are taxed with the same rate, you can have a simple "Taxable Goods" category assigned to all items.

If you sell various products and some of them have different taxes applicable, you could create multiple categories. For example, "Clothing", "Books" and "Food".

Tax Zones
---------

Additionally to tax categories, you can have different tax zones, in order to apply correct taxes for customers coming from any country in the world.
To understand how zones work, please refer to the `Zones <http://docs.sylius.org/en/latest/book/addresses.html#zones>`_ chapter of this book.

Tax Rates
---------

A tax rate is essentially a percentage amount charged based on the sales price. Tax rates also contain other important information:

* Whether product prices are inclusive of this tax
* The zone in which the order address must fall within
* The tax category that a product must belong to in order to be considered taxable
* Calculator to use for computing the tax

Default Tax Zone
----------------

...

Examples
--------

...

Calculators
-----------

...

Final Thoughts
--------------

...

Learn more
----------

* ...
