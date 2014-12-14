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

use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
     * @var RepositoryInterface
     */
    protected $attributeValueRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $attributeValueRepository
     */
    public function __construct(RepositoryInterface $attributeValueRepository)
    {
        $this->attributeValueRepository = $attributeValueRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ArchetypeInterface $archetype, ArchetypeSubjectInterface $subject)
    {
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
            /** @var AttributeValueInterface $attributeValue */
            $attributeValue = $this->attributeValueRepository->createNew();
            $attributeValue->setAttribute($attribute);

            $subject->addAttribute($attributeValue);
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
