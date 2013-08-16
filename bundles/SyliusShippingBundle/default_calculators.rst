Default calculators
===================

Default calculators can be sufficient solution for many use cases.

Flat rate
---------

The ``flat_rate`` calculator, charges concrete amount per shipment.

Per item rate
-------------

The ``per_item_rate`` calculator, charges concrete amount per shipment item.

Flexible rate
-------------

The ``flexible_rate`` calculator, charges one price for the first item, and another price for every other item.

Weight rate
-----------

The ``weight_rate`` calculator, charges one price for certain weight of shipment. So if the shipment weights 5 kg, and calculator is configured to charge $4 per kg, the final price is $20.

More calculators
----------------

Depending on community contributions and Sylius resources, more default calculators can be implemented, for example ``weight_range_rate``.
