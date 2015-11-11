Models
======

.. _component_attribute_model_attribute:

Attribute
---------

Every attribute is represented by the **Attribute** model which by default has the following properties:

+---------------+--------------------------------------+
| Property      | Description                          |
+===============+======================================+
| id            | Unique id of the attribute           |
+---------------+--------------------------------------+
| type          | Attribute's type ('text' by default) |
+---------------+--------------------------------------+
| name          | Attribute's name                     |
+---------------+--------------------------------------+
| configuration | Attribute's configuration            |
+---------------+--------------------------------------+
| values        | Collection of attribute values       |
+---------------+--------------------------------------+
| createdAt     | Date when attribute was created      |
+---------------+--------------------------------------+
| updatedAt     | Date of last attribute update        |
+---------------+--------------------------------------+

.. note::
   This model extends the :ref:`component_translation_model_abstract-translatable` class
   and implements the :ref:`component_attribute_model_attribute-interface`. |br|
   For more detailed information go to `Sylius API Attribute`_.

.. _Sylius API Attribute: http://api.sylius.org/Sylius/Component/Attribute/Model/Attribute.html

.. hint::
   Default attribute types are stored in the :ref:`component_attribute_model_attribute-types` class.

.. _component_attribute_model_attribute-value:

AttributeValue
--------------

This model binds the subject and the attribute,
it is used to store the value of the attribute for the subject.
It has the following properties:

+-----------+----------------------------------+
| Property  | Description                      |
+===========+==================================+
| id        | Unique id of the attribute value |
+-----------+----------------------------------+
| subject   | Reference to attribute's subject |
+-----------+----------------------------------+
| attribute | Reference to an attribute        |
+-----------+----------------------------------+
| value     | Attribute's value                |
+-----------+----------------------------------+

.. note::
   This model implements the :ref:`component_attribute_model_attribute-value-interface`. |br|
   For more detailed information go to `Sylius API AttributeValue`_.

.. _Sylius API AttributeValue: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeValue.html

.. _component_attribute_model_attribute-translation:

AttributeTranslation
--------------------

The attribute's presentation for different locales is represented by the **AttributeTranslation**
model which has the following properties:

+--------------+----------------------------------------+
| Property     | Description                            |
+==============+========================================+
| id           | Unique id of the attribute translation |
+--------------+----------------------------------------+
| presentation | Attribute's name for given locale      |
+--------------+----------------------------------------+

.. note::
   This model extends the :ref:`component_translation_model_abstract-translation` class
   and implements the :ref:`component_attribute_model_attribute-translation-interface`. |br|
   For more detailed information go to `Sylius API AttributeTranslation`_.

.. _Sylius API AttributeTranslation: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeTranslation.html
