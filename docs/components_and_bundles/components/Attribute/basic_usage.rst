Basic Usage
===========

Creating an attributable class
------------------------------

In the following example you will see a minimalistic implementation
of the :ref:`component_attribute_model_attribute-subject-interface`.

.. code-block:: php

    <?php

    namespace App\Model;

    use Doctrine\Common\Collections\Collection;
    use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
    use Sylius\Component\Attribute\Model\AttributeValueInterface;

    class Shirt implements AttributeSubjectInterface
    {
        private Collection $attributes;

        public function getAttributes(): Collection
        {
            return $this->attributes;
        }

        public function setAttributes(Collection $attributes): void
        {
            foreach ($attributes as $attribute) {
                $this->addAttribute($attribute);
            }
        }

        public function addAttribute(AttributeValueInterface $attribute): void
        {
            if (!$this->hasAttribute($attribute)) {
                $attribute->setSubject($this);
                $this->attributes->add($attribute);
            }
        }

        public function removeAttribute(AttributeValueInterface $attribute): void
        {
            if ($this->hasAttribute($attribute)) {
                $attribute->setSubject(null);
                $this->attributes->removeElement($attribute);
            }
        }

        public function hasAttribute(AttributeValueInterface $attribute): bool
        {
            return $this->attributes->contains($attribute);
        }

        public function hasAttributeByCodeAndLocale($attributeCode, $localeCode = null): bool
        {
            return (bool) $this->getAttributeByCodeAndLocale($attributeCode, $localeCode);
        }

        public function getAttributeByCodeAndLocale(string $attributeCode, string $localeCode = null): ?AttributeValueInterface
        {
            return $this->attributes->filter(fn (AttributeValueInterface $attribute) => $attributeCode === $attribute->getCode() &&
                ($attribute->getLocaleCode() === $localeCode || null === $attribute->getLocaleCode()))
                ->first();
        }

        public function getAttributesByLocale(string $localeCode, string $fallbackLocaleCode, ?string $baseLocaleCode = null): Collection
        {
            return $this->attributes->filter(function (AttributeValueInterface $attribute) use ($localeCode) {
                    return $attribute->getLocaleCode() === $localeCode;
                }
            );
        }

        // Optional: you can search attributes by name

        public function hasAttributeByName(string $attributeName): bool
        {
            return (bool) $this->getAttributeByName($attributeName);
        }

        public function getAttributeByName(string $attributeName): Collection
        {
            return $this->attributes->filter(fn ($attribute) => $attributeName === $attribute->getName());
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
Doctrine's ``Collection`` interface, e.g. the ``ArrayCollection`` class.

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

If you are using the optional functions that checks attributes by name you can access them by this value

.. code-block:: php

    <?php

    $shirt->hasAttributeByName('Size'); // returns true

    $shirt->getAttributeByName('Size'); // returns $smallSize

Removing an attribute
---------------------

.. code-block:: php

    <?php

    // in example implementation, removeAttribute function checks if collection has attribute
    $shirt->hasAttribute($smallSize); // returns true

    $shirt->removeAttribute($smallSize);

    $shirt->hasAttribute($smallSize); // now returns false
