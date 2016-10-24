<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Attribute\AttributeType;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class DateAttributeTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateAttributeType::class);
    }

    function it_implements_attribute_type_interface()
    {
        $this->shouldImplement(AttributeTypeInterface::class);
    }

    function its_storage_type_is_text()
    {
        $this->getStorageType()->shouldReturn('date');
    }

    function its_type_is_text()
    {
        $this->getType()->shouldReturn('date');
    }
}
