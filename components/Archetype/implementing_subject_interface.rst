Implementing ArchetypeSubjectInterface
======================================

To characterize an object with attributes and options from a common archetype,the object class needs to implement
the ``ArchetypeSubjectInterface``.

+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| Method                                      | Description                                                         | Returned value             |
+=============================================+=====================================================================+============================+
| getArchetype()                              | Returns the archetype of the subject.                               | ArchetypeInterface         |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| setArchetype(ArchetypeInterface $archetype) | Sets the archetype of the subject.                                  | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
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
| getMasterVariant()                          | Returns the master variant of the object.                           | VariantInterface           |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| setMasterVariant(VariantInterface $variant) | Sets the master variant of the object.                              | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasVariants()                               | Checks whether there are any variants other than the master variant.| Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| getVariants()                               | Returns all object variants, including the master variant.          | VariantInterface[]         |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| setVariants(Collection $variants)           | Sets all variants on an object.                                     | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| addVariant(VariantInterface $variant)       | Adds a variant to an object.                                        | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| removeVariant(VariantInterface $variant)    | Removes variant from object.                                        | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasVariant(VariantInterface $variant)       | Checks whether an object has a given variant.                       | Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasOptions()                                | Returns true when the object has one or more options.               | Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| getOptions()                                | Returns all options of an object.                                   | OptionInterface[]          |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| setOptions(Collection $options)             | Sets all options on an object.                                      | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| addOption(OptionInterface $option)          | Adds an option to the object.                                       | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| removeOption(OptionInterface $option)       | Removes option from product.                                        | Void                       |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
| hasOption(OptionInterface $option)          | Checks whether object has given option.                             | Boolean                    |
+---------------------------------------------+---------------------------------------------------------------------+----------------------------+
