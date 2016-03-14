<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Salvatore Pappalardo <salvatore.pappalardo82@gmail.com>
 */
class AttributeValueTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('AttributeValueTranslation', ['sylius'], 'server');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueTranslationType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('value', 'sylius_attribute_type_text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, ['valueTranslationType' => 'text']);
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver->setRequired('valueTranslationType')->shouldBeCalled();
        $resolver->setDefaults([
            'data_class' => 'AttributeValueTranslation',
            'validation_groups' => ['sylius'],
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_server_attribute_value_translation');
    }
}
