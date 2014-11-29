Implementing AttributeSubjectInterface
======================================

To characterize an object with attributes, the object class needs to implement the ``AttributeSubjectInterface``.

+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| Method                                      | Description                                                         | Returned value             |
+=============================================+=====================================================================+============================+
| getAttributes()                             | Returns all attributes of the subject.                              | AttributeValueInterface[]  |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| setAttributes(Collection $attributes)       | Sets all attributes of the subject.                                 | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| addAttribute(AttributeValue $attribute)     | Adds an attribute to the subject.                                   | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| removeAttribute(AttributeValue $attribute)  | Removes an attribute from the subject.                              | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasAttribute(AttributeValue $attribute)     | Checks whether the subject has a given attribute.                   | Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasAttributeByName($attributeName)          | Checks whether the subject has a given attribute, access by name.   | Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| getAttributeByName($attributeName)          | Returns an attribute of the subject by its name.                    | AttributeValueInterface    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+