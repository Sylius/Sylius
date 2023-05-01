.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Shipment
--------

**Shipment** object has methods to represent the events that take place during the process of shipment.
Shipment has the following properties:

+-----------+----------------------------------------------+
| Property  | Description                                  |
+===========+==============================================+
| id        | Unique id of the shipment                    |
+-----------+----------------------------------------------+
| state     | Reference to constant from ShipmentInterface |
+-----------+----------------------------------------------+
| method    | Reference to ShippingMethod                  |
+-----------+----------------------------------------------+
| items     | Reference to Collection of shipping items    |
+-----------+----------------------------------------------+
| tracking  | Tracking code for shipment                   |
+-----------+----------------------------------------------+
| createdAt | Creation time                                |
+-----------+----------------------------------------------+
| updatedAt | Last update time                             |
+-----------+----------------------------------------------+

.. note::
    This model implements the :ref:`component_shipping_model_shipment-interface`.

ShipmentItem
------------

**ShipmentItem** object is used for connecting a shippable object with a proper shipment.
ShipmentItems have the following properties:

+---------------+----------------------------------------------+
| Property      | Description                                  |
+===============+==============================================+
| id            | Unique id of the ShipmentItem                |
+---------------+----------------------------------------------+
| shipment      | Reference to Shipment                        |
+---------------+----------------------------------------------+
| shippable     | Reference to shippable object                |
+---------------+----------------------------------------------+
| shippingState | Reference to constant from ShipmentInterface |
+---------------+----------------------------------------------+
| createdAt     | Creation time                                |
+---------------+----------------------------------------------+
| updatedAt     | Last update time                             |
+---------------+----------------------------------------------+

.. note::
    This model implements the :ref:`component_shipping_model_shipment-item-interface`.

ShippingCategory
----------------

**ShippingCategory** object represents category which can be common for **ShippingMethod** and object which implements
**ShippableInterface**.
ShippingCategory has the following properties:

+---------------+-------------------------------------+
| Property      | Description                         |
+===============+=====================================+
| id            | Unique id of the ShippingCategory   |
+---------------+-------------------------------------+
| code          | Unique code of the ShippingCategory |
+---------------+-------------------------------------+
| name          | e.g. "Regular"                      |
+---------------+-------------------------------------+
| description   | e.g. “Regular weight items”         |
+---------------+-------------------------------------+
| createdAt     | Creation time                       |
+---------------+-------------------------------------+
| updatedAt     | Last update time                    |
+---------------+-------------------------------------+

.. hint::
    To understand relationship between **ShippingMethod** and shippable object base on **ShippingCategory** go to
    :doc:`/components_and_bundles/bundles/SyliusShippingBundle/shipping_requirements`.

.. note::
    This model implements the :ref:`component_shipping_model_shipping-category-interface`.

ShippingMethod
--------------

**ShippingMethod** object represents method of shipping allowed for given shipment.
It has the following properties:

+---------------------+-------------------------------------------------------------------------+
| Property            | Description                                                             |
+=====================+=========================================================================+
| id                  | Unique id of the ShippingMethod                                         |
+---------------------+-------------------------------------------------------------------------+
| code                | Unique code of the ShippingMethod                                       |
+---------------------+-------------------------------------------------------------------------+
| category            | e.g. "Regular"                                                          |
+---------------------+-------------------------------------------------------------------------+
| categoryRequirement | Reference to constant from ShippingMethodInterface                      |
+---------------------+-------------------------------------------------------------------------+
| enabled             | Boolean flag of enablement                                              |
+---------------------+-------------------------------------------------------------------------+
| calculator          | Reference to constant from DefaultCalculators                           |
+---------------------+-------------------------------------------------------------------------+
| configuration       | Extra configuration for calculator                                      |
+---------------------+-------------------------------------------------------------------------+
| rules               | Collection of Rules                                                     |
+---------------------+-------------------------------------------------------------------------+
| createdAt           | Creation time                                                           |
+---------------------+-------------------------------------------------------------------------+
| updatedAt           | Last update time                                                        |
+---------------------+-------------------------------------------------------------------------+
| currentTranslation  | Translation chosen from translations list accordingly to current locale |
+---------------------+-------------------------------------------------------------------------+
| currentLocale       | Currently set locale                                                    |
+---------------------+-------------------------------------------------------------------------+
| translations        | Collection of translations                                              |
+---------------------+-------------------------------------------------------------------------+
| fallbackLocale      | Locale used in case no translation is available                         |
+---------------------+-------------------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_shipping_model_shipping-method-interface` and uses the
    `TranslatableTrait <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TranslatableTrait.php>`_.

ShippingMethodTranslation
-------------------------

**ShippingMethodTranslation** object allows to translate the shipping method's name accordingly to the provided locales.
It has the following properties:

+--------------+-----------------------------------------------------+
| Property     | Description                                         |
+==============+=====================================================+
| id           | Unique id of the ShippingMethodTranslation          |
+--------------+-----------------------------------------------------+
| name         | e.g. "FedEx"                                        |
+--------------+-----------------------------------------------------+
| locale       | Translation locale                                  |
+--------------+-----------------------------------------------------+
| translatable | The translatable model assigned to this translation |
+--------------+-----------------------------------------------------+

.. note::
    This model implements the :ref:`component_shipping_model_shipping-method-translation-interface` and extends
    `AbstractTranslation <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/AbstractTranslation.php>`_ class.

