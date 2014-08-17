Implementing AttributeSubjectInterface
======================================

To characterize an object with attributes, the object class needs to implement the ``AttributeSubjectInterface``.

* ``getAttributes()``
* ``setAttributes(Collection $attributes)``
* ``addAttribute(AttributeValue $attribute)``
* ``removeAttribute(AttributeValue $attribute)``
* ``hasAttribute(AttributeValue $attribute)``
* ``hasAttributeByName($attributeName)``
* ``getAttributeByName($attributeName)``
