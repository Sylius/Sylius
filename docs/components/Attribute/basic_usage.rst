Basic Usage
===========

Creating an attributable class
------------------------------

In the following example you will see a minimalistic implementation
of the :ref:`component_attribute_model_attribute-subject-interface`.

.. code-block:: php

   <?php

   namespace App\Model;

   use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
   use Sylius\Component\Attribute\Model\AttributeValueInterface;
   use Doctrine\Common\Collections\Collection;

   class Shirt implements AttributeSubjectInterface
   {
       /**
        * @var AttributeValueInterface[]
        */
       private $attributes;

       /**
        * {@inheritdoc}
        */
       public function getAttributes()
       {
           return $this->attributes;
       }

       /**
        * {@inheritdoc}
        */
       public function setAttributes(Collection $attributes)
       {
           foreach ($attributes as $attribute) {
               $this->addAttribute($attribute);
           }
       }

       /**
        * {@inheritdoc}
        */
       public function addAttribute(AttributeValueInterface $attribute)
       {
           if (!$this->hasAttribute($attribute)) {
               $attribute->setSubject($this);
               $this->attributes[] = $attribute;
           }
       }

       /**
        * {@inheritdoc}
        */
       public function removeAttribute(AttributeValueInterface $attribute)
       {
           if ($this->hasAttribute($attribute)){
               $attribute->setSubject(null);
               $key = array_search($attribute, $this->attributes);
               unset($this->attributes[$key]);
           }
       }

       /**
        * {@inheritdoc}
        */
       public function hasAttribute(AttributeValueInterface $attribute)
       {
           return in_array($attribute, $this->attributes);
       }

       /**
        * {@inheritdoc}
        */
       public function hasAttributeByName($attributeName)
       {
           foreach ($this->attributes as $attribute) {
               if ($attribute->getName() === $attributeName) {
                   return true;
               }
           }

           return false;
       }

       /**
        * {@inheritdoc}
        */
       public function getAttributeByName($attributeName)
       {
           foreach ($this->attributes as $attribute) {
               if ($attribute->getName() === $attributeName) {
                   return $attribute;
               }
           }

           return null;
       }
       
       /**
        * {@inheritdoc}
        */
       public function hasAttributeByCodeAndLocale($attributeCode, $localeCode = null)
       {
   
       }

       /**
        * {@inheritdoc}
        */
       public function getAttributeByCodeAndLocale($attributeCode, $localeCode = null)
       {
   
       }
   }

.. note::
   An implementation similar to the one above has been done in the :ref:`component_product_model_product` model.

Adding attributes to an object
------------------------------

Once we have our class we can characterize it with attributes.

.. code-block:: php

   <?php

   use App\Model\Shirt;
   use Sylius\Component\Attribute\Model\Attribute;
   use Sylius\Component\Attribute\Model\AttributeValue;
   use Sylius\Component\Attribute\AttributeType\TextAttributeType;
   use Sylius\Component\Attribute\Model\AttributeValueInterface;

   $attribute = new Attribute();
   $attribute->setName('Size');
   $attribute->setType(TextAttributeType::TYPE);
   $attribute->setStorageType(AttributeValueInterface::STORAGE_TEXT);

   $smallSize = new AttributeValue();
   $mediumSize = new AttributeValue();
   
   $smallSize->setAttribute($attribute);
   $mediumSize->setAttribute($attribute);

   $smallSize->setValue('S');
   $mediumSize->setValue('M');

   $shirt = new Shirt();

   $shirt->addAttribute($smallSize);
   $shirt->addAttribute($mediumSize);

Or you can just add all attributes needed using a class implementing
Doctrine's `Collection`_ interface, e.g. the `ArrayCollection`_ class.

.. _Collection: http://www.doctrine-project.org/api/common/2.2/class-Doctrine.Common.Collections.Collection.html
.. _ArrayCollection: http://www.doctrine-project.org/api/common/2.2/class-Doctrine.Common.Collections.ArrayCollection.html

.. warning::
   Beware! It's really important to set proper attribute storage type, which should reflect value type that is set in `AttributeValue`.

.. code-block:: php

   <?php

   use Doctrine\Common\Collections\ArrayCollection;

   $attributes = new ArrayCollection();

   $attributes->add($smallSize);
   $attributes->add($mediumSize);

   $shirt->setAttributes($attributes);

.. note::
   Notice that you don't actually add an :ref:`component_attribute_model_attribute` to the subject,
   instead you need to add every :ref:`component_attribute_model_attribute-value` assigned to the attribute.

Accessing attributes
--------------------

.. code-block:: php

   <?php

   $shirt->getAttributes(); // returns an array containing all set attributes

   $shirt->hasAttribute($smallSize); // returns true
   $shirt->hasAttribute($hugeSize); // returns false

Accessing attributes by name
----------------------------

.. code-block:: php

   <?php

   $shirt->hasAttributeByName('Size'); // returns true

   $shirt->getAttributeByName('Size'); // returns $smallSize

Removing an attribute
---------------------

.. code-block:: php

   <?php

   $shirt->hasAttribute($smallSize); // returns true

   $shirt->removeAttribute($smallSize);

   $shirt->hasAttribute($smallSize); // now returns false
