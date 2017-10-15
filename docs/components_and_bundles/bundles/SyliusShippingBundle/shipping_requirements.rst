Shipping method requirements
============================

Sylius has a very flexible system for displaying only the right shipping methods to the user.

Shipping categories
-------------------

Every **ShippableInterface** can hold a reference to **ShippingCategory**.
The **ShippingSubjectInterface** (or **ShipmentInterface**) returns a collection of shippables.

**ShippingMethod** has an optional shipping category setting as well as **categoryRequirement** which has 3 options.
If this setting is set to null, categories system is ignored.

"Match any" requirement
~~~~~~~~~~~~~~~~~~~~~~~

With this requirement, the shipping method will support any shipment (or shipping subject) which contains at least one shippable with the same category.

"Match all" requirement
~~~~~~~~~~~~~~~~~~~~~~~

All shippables have to reference the same category as the **ShippingMethod**.

"Match none" requirement
~~~~~~~~~~~~~~~~~~~~~~~~

None of the shippables can have the same shipping category.

