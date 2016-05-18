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
use Sylius\Component\Variation\Model\VariableInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VariantChoiceTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('variable_name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\Type\VariantChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->addViewTransformer(Argument::type(
            CollectionToArrayTransformer::class
        ))->shouldBeCalled();

        $this->buildForm($builder, [
            'multiple' => true,
        ]);
    }

    function it_has_options(OptionsResolver $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);

        $resolver->setRequired([
            'variable',
        ])->shouldBeCalled()->willReturn($resolver);

        $resolver->setAllowedTypes('variable', VariableInterface::class)->shouldBeCalled()->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_variable_name_variant_choice');
    }
}
