<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Archetype\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Archetype\Model\Archetype');
    }

    function it_is_an_Archetype()
    {
        $this->shouldImplement('Sylius\Component\Archetype\Model\ArchetypeInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('T-Shirt size');
        $this->getName()->shouldReturn('T-Shirt size');
    }

    function it_initializes_attribute_collection_by_default()
    {
        $this->getAttributes()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_attribute_collection_is_mutable(Collection $attributes)
    {
        $this->setAttributes($attributes);
        $this->getAttributes()->shouldReturn($attributes);
    }

    function it_adds_attribute(AttributeInterface $attribute)
    {
        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);
    }

    function it_removes_attribute(AttributeInterface $attribute)
    {
        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);

        $this->removeAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(false);
    }

    function it_initializes_option_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_option_collection_is_mutable(Collection $attributes)
    {
        $this->setOptions($attributes);
        $this->getOptions()->shouldReturn($attributes);
    }

    function it_adds_option(OptionInterface $attribute)
    {
        $this->addOption($attribute);
        $this->hasOption($attribute)->shouldReturn(true);
    }

    function it_removes_option(OptionInterface $attribute)
    {
        $this->addOption($attribute);
        $this->hasOption($attribute)->shouldReturn(true);

        $this->removeOption($attribute);
        $this->hasOption($attribute)->shouldReturn(false);
    }

    function it_has_no_parent_by_default()
    {
        $this->hasParent()->shouldReturn(false);
    }

    function its_parent_is_mutable(ArchetypeInterface $parent)
    {
        $this->setParent($parent);
        $this->getParent()->shouldReturn($parent);
        $this->hasParent()->shouldReturn(true);
    }

    function it_has_fluent_interface(Collection $attributes, AttributeInterface $attribute)
    {
        $date = new \DateTime();

        $this->setName('T-Shirt')->shouldReturn($this);
        $this->setAttributes($attributes)->shouldReturn($this);
        $this->addAttribute($attribute)->shouldReturn($this);
        $this->removeAttribute($attribute)->shouldReturn($this);
        $this->setCreatedAt($date)->shouldReturn($this);
        $this->setUpdatedAt($date)->shouldReturn($this);
    }
}
