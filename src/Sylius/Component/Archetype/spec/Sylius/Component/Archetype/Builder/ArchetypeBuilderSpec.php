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
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeBuilderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $attributeValueRepository)
    {
        $this->beConstructedWith($attributeValueRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Archetype\Builder\ArchetypeBuilder');
    }

    function it_is_an_Archetype_Builder()
    {
        $this->shouldImplement('Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface');
    }

    function it_assigns_archetype_attributes_and_options_to_product(
        $attributeValueRepository,
        ArchetypeInterface $archetype,
        ArchetypeSubjectInterface $subject,
        AttributeInterface $attribute,
        AttributeValueInterface $attributeValue,
        OptionInterface $option
    ) {
        $archetype->getAttributes()->willReturn(array($attribute))->shouldBeCalled();
        $archetype->getOptions()->willReturn(array($option))->shouldBeCalled();

        $attributeValueRepository->createNew()->shouldBeCalled()->willReturn($attributeValue);
        $attributeValue->setAttribute($attribute)->shouldBeCalled();

        $subject->addAttribute($attributeValue)->shouldBeCalled();
        $subject->addOption($option)->shouldBeCalled();

        $this->build($archetype, $subject);
    }
}
