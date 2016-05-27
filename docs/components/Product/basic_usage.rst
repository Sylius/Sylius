Basic Usage
===========

Creating a product
------------------

.. code-block:: php

   <?php

   use Sylius\Component\Product\Model\Product;

   $product = new Product();

   $product->getCreatedAt(); // Returns the \DateTime when it was created.
   $product->getAvailableOn(); // By default returns the same value as getCreatedAt.

Setting the archetype
---------------------

.. code-block:: php

   <?php

   use Sylius\Component\Product\Model\Archetype;
   use Sylius\Component\Product\Model\AttributeValue;

   $smallSize = new AttributeValue();
   $smallSize->setValue('Small');

   $archetype = new Archetype();
   $archetype->addAttribute($smallSize);

   $product->setArchetype($archetype);

.. note::
   Doesn't matter if you use **Archetype** from this component,
   the :doc:`/components/Archetype/index` or your custom.
   Every model implementing the :ref:`component_archetype_model_archetype-interface` is supported.

Product attributes management
-----------------------------

.. code-block:: php

   <?php

   use Sylius\Component\Product\Model\Attribute;
   use Doctrine\Common\Collections\ArrayCollection;

   $attribute = new Attribute();

   $colorGreen = new AttributeValue();
   $colorRed = new AttributeValue();

   $attributes = new ArrayCollection();

   $attribute->setName('Color');

   $colorGreen->setValue('Green');
   $colorRed->setValue('Red');

   $colorGreen->setAttribute($attribute);
   $colorRed->setAttribute($attribute);

   $product->addAttribute($colorGreen);
   $product->hasAttribute($colorGreen); // Returns true.
   $product->removeAttribute($colorGreen);

   $attributes->add($colorGreen);
   $attributes->add($colorRed);
   $product->setAttributes($attributes);

   $product->hasAttributeByName('Color');
   $product->getAttributeByName('Color'); // Returns $colorGreen.

   $product->getAttributes(); // Returns $attributes.

.. note::
   Only instances of **AttributeValue** from the :doc:`/components/Product/index`
   component can be used with the :ref:`component_product_model_product` model.

.. hint::
   The ``getAttributeByName`` will only return the first occurrence of **AttributeValue**
   assigned to the **Attribute** with specified name, the rest will be omitted.

Product variants management
---------------------------

.. code-block:: php

   <?php

   use Sylius\Component\Product\Model\Variant;

   $variant = new Variant();
   $availableVariant = new Variant();

   $variants = new ArrayCollection();

   $availableVariant->setAvailableOn(new \DateTime());

   $product->hasVariants(); // return false

   $product->addVariant($variant);
   $product->hasVariant($variant); // returns true
   $product->hasVariants(); // returns true
   $product->removeVariant($variant);

   $variants->add($variant);
   $variants->add($availableVariant);

   $product->setVariants($variants);

   $product->getVariants(); // Returns an array containing $variant and $availableVariant.

.. code-block:: php

   $product->getAvailableVariants(); // Returns an array containing only $availableVariant.

``getAvailableVariants`` returns only variants which ``availableOn`` property is set to a past time.

.. note::
   Only instances of **Variant** from the :doc:`/components/Product/index` component
   can be used with the :ref:`component_product_model_product` model.

Product options management
--------------------------

.. code-block:: php

   <?php

   use Sylius\Component\Product\Model\Option;

   $firstOption = new Option();
   $secondOption = new Option();

   $options = new ArrayCollection();

   $product->addOption($firstOption);
   $product->hasOption($firstOption); // Returns true.
   $product->removeOption($firstOption);

   $options->add($firstOption);
   $options->add($secondOption);

   $product->setOptions($options);
   $product->hasOptions(); // Returns true.
   $product->getOptions(); // Returns an array containing all inserted options.

.. note::
   Same as in **Archetype** case, you can use **Option** objects from this component,
   the :doc:`/components/Variation/index` or your custom.
   Every model implementing the :ref:`component_variation_model_option-interface` is supported.
