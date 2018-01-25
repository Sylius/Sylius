<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Attribute\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Attribute\Model\Attribute;
use Sylius\Component\Attribute\Model\AttributeInterface;

final class AttributeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Attribute::class);
    }

    function it_implements_attribute_interface(): void
    {
        $this->shouldImplement(AttributeInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('t_shirt_collection');
        $this->getCode()->shouldReturn('t_shirt_collection');
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('T-Shirt collection');
        $this->getName()->shouldReturn('T-Shirt collection');
    }

    function it_returns_name_when_converted_to_string(): void
    {
        $this->setName('T-Shirt material');
        $this->__toString()->shouldReturn('T-Shirt material');
    }

    function it_has_text_type_by_default(): void
    {
        $this->getType()->shouldReturn(TextAttributeType::TYPE);
    }

    function its_type_is_mutable(): void
    {
        $this->setType(CheckboxAttributeType::TYPE);
        $this->getType()->shouldReturn(CheckboxAttributeType::TYPE);
    }

    function it_initializes_empty_configuration_array_by_default(): void
    {
        $this->getConfiguration()->shouldReturn([]);
    }

    function its_configuration_is_mutable(): void
    {
        $this->setConfiguration(['format' => 'd/m/Y']);
        $this->getConfiguration()->shouldReturn(['format' => 'd/m/Y']);
    }

    function its_storage_type_is_mutable(): void
    {
        $this->setStorageType('text');
        $this->getStorageType()->shouldReturn('text');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function its_creation_date_is_mutable(\DateTime $date): void
    {
        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(\DateTime $date): void
    {
        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }

    function it_has_position(): void
    {
        $this->getPosition()->shouldReturn(null);
        $this->setPosition(0);
        $this->getPosition()->shouldReturn(0);
    }
}
