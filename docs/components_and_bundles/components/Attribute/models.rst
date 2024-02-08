Models
======

.. _component_attribute_model_attribute:

Attribute
---------

Every attribute is represented by the **Attribute** model which by default has the following properties:

+---------------+-----------------------------------------------------------+
| Property      | Description                                               |
+===============+===========================================================+
| id            | Unique id of the attribute                                |
+---------------+-----------------------------------------------------------+
| type          | Attribute's type ('text' by default)                      |
+---------------+-----------------------------------------------------------+
| name          | Attribute's name (from AttributeTranslation)              |
+---------------+-----------------------------------------------------------+
| configuration | Attribute's configuration                                 |
+---------------+-----------------------------------------------------------+
| translatable  | Attribute possibility to be translated                    |
+---------------+-----------------------------------------------------------+
| storageType   | Defines how attribute value should be stored in database  |
+---------------+-----------------------------------------------------------+
| createdAt     | Date when attribute was created                           |
+---------------+-----------------------------------------------------------+
| updatedAt     | Date of last attribute update                             |
+---------------+-----------------------------------------------------------+

.. note::
    This model uses the `TranslatableTrait <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TranslatableTrait.php>`_
    and implements the :ref:`component_attribute_model_attribute-interface`.

    For more detailed information go to `Sylius Attribute Component Attribute <https://github.com/Sylius/Attribute/blob/master/Model/Attribute.php>`_.

.. attention::
    Attribute's type is an alias of AttributeType service.

.. _component_attribute_model_attribute-value:

AttributeValue
--------------

This model binds the subject and the attribute,
it is used to store the value of the attribute for the subject.
It has the following properties:

+-----------+---------------------------------------+
| Property  | Description                           |
+===========+=======================================+
| id        | Unique id of the attribute value      |
+-----------+---------------------------------------+
| subject   | Reference to attribute's subject      |
+-----------+---------------------------------------+
| attribute | Reference to an attribute             |
+-----------+---------------------------------------+
| value     | Attribute's value (not mapped)        |
+-----------+---------------------------------------+
| text      | Value of attribute stored as text     |
+-----------+---------------------------------------+
| boolean   | Value of attribute stored as boolean  |
+-----------+---------------------------------------+
| integer   | Value of attribute stored as integer  |
+-----------+---------------------------------------+
| float     | Value of attribute stored as float    |
+-----------+---------------------------------------+
| datetime  | Value of attribute stored as datetime |
+-----------+---------------------------------------+
| date      | Value of attribute stored as date     |
+-----------+---------------------------------------+
| json      | Value of attribute stored as array    |
+-----------+---------------------------------------+

.. attention::
   ``Value`` property is used only as proxy, that stores data in proper field. It's crucial to set attribute value in field, that is mapped as attribute's storage type.

.. note::
   This model implements the :ref:`component_attribute_model_attribute-value-interface`.

    For more detailed information go to `Sylius Attribute Component AttributeValue <https://github.com/Sylius/Attribute/blob/master/Model/AttributeValue.php>`_.

.. _component_attribute_model_attribute-translation:

AttributeTranslation
--------------------

The attribute's name for different locales is represented by the **AttributeTranslation**
model which has the following properties:

+-----------+----------------------------------------+
| Property  | Description                            |
+===========+========================================+
| id        | Unique id of the attribute translation |
+-----------+----------------------------------------+
| name      | Attribute's name for given locale      |
+-----------+----------------------------------------+

.. note::
   This model extends the `AbstractTranslation <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/AbstractTranslation.php>`_ class
   and implements the :ref:`component_attribute_model_attribute-translation-interface`.

   For more detailed information go to `Sylius Attribute Component AttributeTranslation <https://github.com/Sylius/Attribute/blob/master/Model/AttributeTranslation.php>`_.
