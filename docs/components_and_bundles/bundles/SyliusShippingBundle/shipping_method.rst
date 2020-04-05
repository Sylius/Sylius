.. rst-class:: outdated

The Shipping Method
===================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

**ShippingMethod** model represents the way that goods need to be shipped. An example of shipping method may be "DHL Express" or "FedEx World Shipping".

+---------------------+----------------------------------------------+
| Attribute           | Description                                  |
+=====================+==============================================+
| id                  | Unique id of the shipping method             |
+---------------------+----------------------------------------------+
| name                | Name of the shipping method                  |
+---------------------+----------------------------------------------+
| category            | Reference to **ShippingCategory** (optional) |
+---------------------+----------------------------------------------+
| categoryRequirement | Category requirement                         |
+---------------------+----------------------------------------------+
| calculator          | Name of the cost calculator                  |
+---------------------+----------------------------------------------+
| configuration       | Configuration for the calculator             |
+---------------------+----------------------------------------------+
| createdAt           | Date when the method was created             |
+---------------------+----------------------------------------------+
| updatedAt           | Date of the last shipping method update      |
+---------------------+----------------------------------------------+
