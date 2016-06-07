Models
======

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

    For more detailed information go to `Sylius API Shipment`_.

.. _Sylius API Shipment: http://api.sylius.org/Sylius/Component/Shipping/Model/Shipment.html

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

    For more detailed information go to `Sylius API ShipmentItem`_.

.. _Sylius API ShipmentItem: http://api.sylius.org/Sylius/Component/Shipping/Model/ShipmentItem.html


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
    :doc:`/bundles/SyliusShippingBundle/shipping_requirements`.

.. note::
    This model implements the :ref:`component_shipping_model_shipping-category-interface`.

    For more detailed information go to `Sylius API ShippingCategory`_.

.. _Sylius API ShippingCategory: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingCategory.html


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
    :ref:`component_resource_translations_translatable-trait`.

    For more detailed information go to `Sylius API ShippingMethod`_.

.. _Sylius API ShippingMethod: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingMethod.html

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
    :ref:`component_resource_translations_abstract-translation` class.

    Form more information go to `Sylius API ShippingMethodTranslation`_.

.. _Sylius API ShippingMethodTranslation: http://api.sylius.org/Sylius/Component/Shipping/Model/ShippingMethodTranslation.html

Rule
----

A **Rule** object represents additional restrictions which have to be fulfilled by a shippable object in order to be
supported by a given **ShippingMethod**.
Rule has the following properties:

+---------------+------------------------------------------------+
| Property      | Description                                    |
+===============+================================================+
| id            | Unique id of the rule                          |
+---------------+------------------------------------------------+
| type          | Reference to constant from RuleInterface       |
+---------------+------------------------------------------------+
| configuration | Additional restriction which have to be fulfil |
+---------------+------------------------------------------------+
| method        | Reference to ShippingMethod                    |
+---------------+------------------------------------------------+

.. note::
    This model implements the :ref:`component_shipping_model_rule-interface`.

    For more detailed information go to `Sylius API Rule`_.

.. _Sylius API Rule: http://api.sylius.org/Sylius/Component/Shipping/Model/Rule.html
