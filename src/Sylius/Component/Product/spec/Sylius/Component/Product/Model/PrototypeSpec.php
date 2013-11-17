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
use Sylius\Component\Product\Model\PropertyInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PrototypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Product\Model\Prototype');
    }

    function it_implements_Sylius_product_prototype_interface()
    {
        $this->shouldImplement('Sylius\Component\Product\Model\PrototypeInterface');
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

    function it_initializes_property_collection_by_default()
    {
        $this->getProperties()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function its_property_collection_is_mutable(Collection $properties)
    {
        $this->setProperties($properties);
        $this->getProperties()->shouldReturn($properties);
    }

    function it_adds_property(PropertyInterface $property)
    {
        $this->addProperty($property);
        $this->hasProperty($property)->shouldReturn(true);
    }

    function it_removes_property(PropertyInterface $property)
    {
        $this->addProperty($property);
        $this->hasProperty($property)->shouldReturn(true);

        $this->removeProperty($property);
        $this->hasProperty($property)->shouldReturn(false);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_fluent_interface(Collection $properties, PropertyInterface $property)
    {
        $date = new \DateTime();

        $this->setName('T-Shirt')->shouldReturn($this);
        $this->setProperties($properties)->shouldReturn($this);
        $this->addProperty($property)->shouldReturn($this);
        $this->removeProperty($property)->shouldReturn($this);
        $this->setCreatedAt($date)->shouldReturn($this);
        $this->setUpdatedAt($date)->shouldReturn($this);
    }
}
