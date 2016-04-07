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
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType\Configuration\AttributeValueTypeTranslationConfiguration;
use Sylius\Component\Attribute\Model\AttributeInterface;

/**
 * @mixin AttributeValueTypeTranslationConfiguration
 *
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTypeTranslationConfigurationSpec extends ObjectBehavior
{
    function let(AttributeInterface $attribute)
    {
        $this->beConstructedWith($attribute, 'server', 0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValueTypeTranslationConfiguration::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('translations');
    }

    function it_has_label($attribute)
    {
        $attribute->getName()->willReturn('Attribute label');

        $this->getLabel()->shouldReturn('Attribute label');
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('a2lix_translationsForms');
    }

    function it_has_form_options($attribute)
    {
        $attribute->getName()->willReturn('Attribute label');
        $attribute->getType()->willReturn('text');

        $this->getFormOptions()->shouldReturn([
            'form_type' => 'sylius_server_attribute_value_translation',
            'label' => 'Attribute label',
            'form_options' => [
                'attr' => [
                    'data-name' => 'sylius_server[attributes][0][translations]',
                ],
                'value_translation_type' => 'text',
            ],
        ]);
    }
}
