Attributes
==========

Every attribute is represented by the **Attribute** instance and has following properties:

+--------------+-------------------------------------------+
| Attribute    | Description                               |
+==============+===========================================+
| id           | Unique id of the attribute                |
+--------------+-------------------------------------------+
| name         | Name of the attribute ("tshirt_material") |
+--------------+-------------------------------------------+
| presentation | Pretty name visible for user ("Material") |
+--------------+-------------------------------------------+
| type         | Attribute type                            |
+--------------+-------------------------------------------+
| createdAt    | Date when attribute was created           |
+--------------+-------------------------------------------+
| updatedAt    | Date of last attribute update             |
+--------------+-------------------------------------------+


AttributeTypes
==============

The following attribute types are available by default.

+------------+-------------------+
| Type       | Related constant  |
+============+===================+
| text       | TEXT              |
+------------+-------------------+
| number     | NUMBER            |
+------------+-------------------+
| percentage | PERCENTAGE        |
+------------+-------------------+
| checkbox   | CHECKBOX          |
+------------+-------------------+
| choice     | CHOICE            |
+------------+-------------------+
| money      | MONEY             |
+------------+-------------------+

You can get all the the available type by using the static method ``AttributeTypes::getChoices()``

AttributeValues
===============

This model binds the subject and the attribute, it is used to store the value of the attribute for the subject. It has following properties:

+--------------+-------------------------------------------+
| Attribute    | Description                               |
+==============+===========================================+
| subject      | Subject (Example: a product)              |
+--------------+-------------------------------------------+
| attribute    | Attribute (Example: a description)        |
+--------------+-------------------------------------------+
| value        | Value                                     |
+--------------+-------------------------------------------+

.. note::

    ``AttributeValues::getValue()`` will throw ``\BadMethodCallException`` if the attribute is not set