<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Archetype\Builder;

use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Model\VariableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeBuilder implements ArchetypeBuilderInterface
{
    /**
     * Attribute value repository.
     *
     * @var FactoryInterface
     */
    protected $attributeValueFactory;

    /**
     * Constructor.
     *
     * @param FactoryInterface $attributeValueFactory
     */
    public function __construct(FactoryInterface $attributeValueFactory)
    {
        $this->attributeValueFactory = $attributeValueFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ArchetypeSubjectInterface $subject)
    {
        if (null === $archetype = $subject->getArchetype()) {
            return;
        }

        $this->createAndAssignAttributes($archetype, $subject);
        $this->createAndAssignOptions($archetype, $subject);
    }

    /**
     * @param ArchetypeInterface        $archetype
     * @param AttributeSubjectInterface $subject
     */
    private function createAndAssignAttributes(ArchetypeInterface $archetype, AttributeSubjectInterface $subject)
    {
        foreach ($archetype->getAttributes() as $attribute) {
            if (null === $subject->getAttributeByCode($attribute->getCode())) {
                /** @var AttributeValueInterface $attributeValue */
                $attributeValue = $this->attributeValueFactory->createNew();
                $attributeValue->setAttribute($attribute);

                $subject->addAttribute($attributeValue);
            }
        }
    }

    /**
     * @param ArchetypeInterface $archetype
     * @param VariableInterface  $subject
     */
    private function createAndAssignOptions(ArchetypeInterface $archetype, VariableInterface $subject)
    {
        foreach ($archetype->getOptions() as $option) {
            $subject->addOption($option);
        }
    }
}
