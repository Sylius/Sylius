The ShippingSubjectInterface
============================

The find available shipping methods or calculate shipping cost you need to use object implementing ``ShippingSubjectInterface``.

The default **Shipment** model is already implementing ``ShippingSubjectInterface``.

Interface methods
-----------------

* The ``getShippingMethod`` returns a ``ShippingMethodInterface`` instance, representing the method.
* The ``getShippingItemCount`` provides you with the count of items to ship.
* The ``getShippingItemTotal`` returns the total value of shipment, if applicable. The default **Shipment** model returns 0.
* The ``getShippingWeight`` returns the total shipment weight.
* The ``getShippables`` returns a collection of unique ``ShippableInterface`` instances.
