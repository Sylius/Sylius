<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeConfiguration;
use Sylius\Component\Attribute\Model\AttributeInterface;

/**
 * @mixin AttributeValueTypeConfiguration
 *
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeConfigurationSpec extends ObjectBehavior
{
    function let(AttributeInterface $attribute)
    {
        $this->beConstructedWith($attribute, 'server', 0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValueTypeConfiguration::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('value');
    }

    function it_has_label($attribute)
    {
        $attribute->getName()->willReturn('Attribute label');

        $this->getLabel()->shouldReturn('Attribute label');
    }

    function it_has_type($attribute)
    {
        $attribute->getType()->willReturn('date');

        $this->getType()->shouldReturn('sylius_attribute_type_date');
    }

    function it_has_form_options($attribute)
    {
        $attribute->getName()->willReturn('Attribute label');

        $this->getFormOptions()->shouldReturn(['label' => 'Attribute label']);
    }
}
