<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VariantChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('varibale_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->addViewTransformer(Argument::type(
            'Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer'
        ))->shouldBeCalled();

        $this->buildForm($builder, array(
            'multiple' => true
        ));
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);

        $resolver->setRequired(array(
            'variable'
        ))->shouldBeCalled()->willReturn($resolver);

        $resolver->setAllowedTypes(array(
            'variable' => array('Sylius\Component\Variation\Model\VariableInterface')
        ))->shouldBeCalled()->willReturn($resolver);

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_varibale_name_variant_choice');
    }
}
