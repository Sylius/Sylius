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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Attribute\Model\AttributeInterface;
use Sylius\Component\Variation\Model\OptionInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ArchetypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Archetype\Model\Archetype');
    }

    public function it_is_an_Archetype()
    {
        $this->shouldImplement('Sylius\Component\Archetype\Model\ArchetypeInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('T-Shirt size');
        $this->getName()->shouldReturn('T-Shirt size');
    }

    public function it_initializes_attribute_collection_by_default()
    {
        $this->getAttributes()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_initializes_translations_collection_by_default()
    {
        $this->getTranslations()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function its_attribute_collection_is_mutable(Collection $attributes)
    {
        $this->setAttributes($attributes);
        $this->getAttributes()->shouldReturn($attributes);
    }

    public function it_adds_attribute(AttributeInterface $attribute)
    {
        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);
    }

    public function it_removes_attribute(AttributeInterface $attribute)
    {
        $this->addAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(true);

        $this->removeAttribute($attribute);
        $this->hasAttribute($attribute)->shouldReturn(false);
    }

    public function it_initializes_option_collection_by_default()
    {
        $this->getOptions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function its_option_collection_is_mutable(Collection $attributes)
    {
        $this->setOptions($attributes);
        $this->getOptions()->shouldReturn($attributes);
    }

    public function it_adds_option(OptionInterface $attribute)
    {
        $this->addOption($attribute);
        $this->hasOption($attribute)->shouldReturn(true);
    }

    public function it_removes_option(OptionInterface $attribute)
    {
        $this->addOption($attribute);
        $this->hasOption($attribute)->shouldReturn(true);

        $this->removeOption($attribute);
        $this->hasOption($attribute)->shouldReturn(false);
    }

    public function it_has_no_parent_by_default()
    {
        $this->hasParent()->shouldReturn(false);
    }

    public function its_parent_is_mutable(ArchetypeInterface $parent)
    {
        $this->setParent($parent);
        $this->getParent()->shouldReturn($parent);
        $this->hasParent()->shouldReturn(true);
    }

    public function it_has_fluent_interface(Collection $attributes, AttributeInterface $attribute)
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
