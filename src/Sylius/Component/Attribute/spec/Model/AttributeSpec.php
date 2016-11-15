<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class AttributeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Attribute::class);
    }

    function it_implements_attribute_interface()
    {
        $this->shouldImplement(AttributeInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('t_shirt_collection');
        $this->getCode()->shouldReturn('t_shirt_collection');
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('T-Shirt collection');
        $this->getName()->shouldReturn('T-Shirt collection');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('T-Shirt material');
        $this->__toString()->shouldReturn('T-Shirt material');
    }

    function it_has_text_type_by_default()
    {
        $this->getType()->shouldReturn(TextAttributeType::TYPE);
    }

    function its_type_is_mutable()
    {
        $this->setType(CheckboxAttributeType::TYPE);
        $this->getType()->shouldReturn(CheckboxAttributeType::TYPE);
    }

    function it_initializes_empty_configuration_array_by_default()
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(['format' => 'd/m/Y']);
        $this->getConfiguration()->shouldReturn(['format' => 'd/m/Y']);
    }

    function its_storage_type_is_mutable()
    {
        $this->setStorageType('text');
        $this->getStorageType()->shouldReturn('text');
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_creation_date_is_mutable(\DateTime $date)
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date)
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_position()
    {
        $this->getPosition()->shouldReturn(null);
        $this->setPosition(0);
        $this->getPosition()->shouldReturn(0);
    }
}
