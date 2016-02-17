<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Archetype\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeBuilderSpec extends ObjectBehavior
{
    function let(FactoryInterface $attributeValueFactory)
    {
        $this->beConstructedWith($attributeValueFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Archetype\Builder\ArchetypeBuilder');
    }

    function it_is_an_Archetype_Builder()
    {
        $this->shouldImplement(ArchetypeBuilderInterface::class);
    }

    function it_does_not_build_the_subject_if_it_has_no_archetype(ArchetypeSubjectInterface $subject)
    {
        $subject->getArchetype()->willReturn(null);

        $subject->addAttribute(Argument::any())->shouldNotBeCalled();
        $subject->addOption(Argument::any())->shouldNotBeCalled();

        $this->build($subject);
    }

    function it_assigns_archetype_attributes_and_options_to_the_subject(
        $attributeValueFactory,
        ArchetypeInterface $archetype,
        ArchetypeSubjectInterface $subject,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        OptionInterface $option
    ) {
        $archetype->getAttributes()->willReturn([$attribute])->shouldBeCalled();
        $archetype->getOptions()->willReturn([$option])->shouldBeCalled();

        $attribute->getCode()->willReturn('test');
        $subject->getAttributeByCode('test')->shouldBeCalled()->willReturn(null);

        $attributeValueFactory->createNew()->shouldBeCalled()->willReturn($attributeValue);
        $attributeValue->setAttribute($attribute)->shouldBeCalled();

        $subject->getArchetype()->willReturn($archetype);
        $subject->addAttribute($attributeValue)->shouldBeCalled();
        $subject->addOption($option)->shouldBeCalled();

        $this->build($subject);
    }

    function it_creates_new_values_only_for_non_existing_attributes(
        $attributeValueFactory,
        ArchetypeInterface $archetype,
        ArchetypeSubjectInterface $subject,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        OptionInterface $option
    ) {
        $archetype->getAttributes()->willReturn([$attribute])->shouldBeCalled();
        $archetype->getOptions()->willReturn([$option])->shouldBeCalled();

        $attribute->getCode()->willReturn('test');
        $subject->getAttributeByCode('test')->shouldBeCalled()->willReturn($attributeValue);

        $attributeValueFactory->createNew()->shouldNotBeCalled();
        $attributeValue->setAttribute($attribute)->shouldNotBeCalled();

        $subject->getArchetype()->willReturn($archetype);
        $subject->addAttribute(Argument::any())->shouldNotBeCalled();
        $subject->addOption($option)->shouldBeCalled();

        $this->build($subject);
    }
}
