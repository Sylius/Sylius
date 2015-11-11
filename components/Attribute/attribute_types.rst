.. _component_attribute_model_attribute-types:

AttributeTypes
==============

The following attribute types are available by default in the **AttributeTypes** class:

+------------------+------------+
| Related constant | Type       |
+==================+============+
| TEXT             | text       |
+------------------+------------+
| NUMBER           | number     |
+------------------+------------+
| PERCENTAGE       | percentage |
+------------------+------------+
| CHECKBOX         | checkbox   |
+------------------+------------+
| CHOICE           | choice     |
+------------------+------------+
| MONEY            | money      |
+------------------+------------+

.. hint::
   Use the static method ``AttributeTypes::getChoices()`` to get an array containing all types.

.. note::
   For more detailed information go to `Sylius API AttributeTypes`_.

.. _Sylius API AttributeTypes: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeTypes.html
