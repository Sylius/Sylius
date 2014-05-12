Implementing SubjectInterface
=============================

To characterize an object using attribute, it needs to implement the **SubjectInterface** interface:

* ``getAttributes()``
* ``setAttributes(Collection $attributes)``
* ``addAttribute(AttributeValue $attribute)``
* ``removeAttribute(AttributeValue $attribute)``
* ``hasAttribute(AttributeValue $attribute)``
* ``hasAttributeByName($attributeName)``
* ``getAttributeByName($attributeName)``
