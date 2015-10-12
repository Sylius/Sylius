Basic Usage
===========

The usage of this component bases on a few classes cooperation.
Create an **Archetype** with given **Attributes** as an *ArrayCollection*.
To create an **Archetype** we can use its Builder class, which will use
your **AttributeValues** repository.

Archetype
---------

.. code-block:: php

    <?php

    use Doctrine\Common\Collections\ArrayCollection;
    use Sylius\Component\Attribute\Model\Attribute;
    use Sylius\Component\Archetype\Model\Archetype;
    use Sylius\Component\Archetype\Builder\ArchetypeBuilder;

    $archetype = new Archetype();

    // Before you can start using the new archetype please set its current and fallback locales.
    $archetype->setCurrentLocale('en');
    $archetype->setFallbackLocale('en');

    // Let's create an attribute for our archetype
    $attribute = new Attribute();
    $attribute->setName('Mug material');
    $attribute->setType('text');

    // And then let's add it to a collection
    $attributes = new ArrayCollection();
    $attributes->add($attribute);

    $archetype->setName('Mug');
    $archetype->setAttributes($attributes);
    $archetype->getName(); // returns 'Mug'
    $archetype->getAttributes(); // returns $attributes


ArchetypeBuilder
----------------

.. code-block:: php

    /**
     * @param RepositoryInterface $attributeValueRepository
     */
    $builder = new ArchetypeBuilder($attributeValueRepository);

    /**
     * @var ArchetypeSubjectInterface $subject
     */
    $subject = /* ... */;

    $builder->build($subject);

.. note::

    You can find more information about this class in `Sylius API ArchetypeBuilder`_.

.. _Sylius API ArchetypeBuilder: http://api.sylius.org/Sylius/Component/Archetype/Builder/ArchetypeBuilder.html
