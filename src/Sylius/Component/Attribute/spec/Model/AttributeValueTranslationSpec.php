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
use Sylius\Component\Attribute\Model\AttributeValueTranslationInterface;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Attribute\Model\AttributeValueTranslation');
    }

    function it_implements_attribute_value_translation_interface()
    {
        $this->shouldImplement(AttributeValueTranslationInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_value_by_default()
    {
        $this->getValue()->shouldReturn(null);
    }

    function its_value_is_mutable()
    {
        $this->setValue('Size');
        $this->getValue()->shouldReturn('Size');
    }
}
