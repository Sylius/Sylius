<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\ArchetypeInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArchetypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Archetype');
    }

    public function it_implements_Sylius_product_archetype_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\ArchetypeInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_initializes_attribute_collection_by_default()
    {
        $this->getAttributes()->shouldHaveType('Doctrine\Common\Collections\Collection');
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

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
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

        $this->setAttributes($attributes)->shouldReturn($this);
        $this->addAttribute($attribute)->shouldReturn($this);
        $this->removeAttribute($attribute)->shouldReturn($this);
        $this->setCreatedAt($date)->shouldReturn($this);
        $this->setUpdatedAt($date)->shouldReturn($this);
    }
}
