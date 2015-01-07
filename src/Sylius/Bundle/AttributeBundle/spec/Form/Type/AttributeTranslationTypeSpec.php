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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class AttributeTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('AttributeTranslation', array('sylius'), 'server');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AttributeBundle\Form\Type\AttributeTranslationType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('presentation', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AttributeTranslation', 'validation_groups' => array('sylius')))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_server_attribute_translation');
    }
}
