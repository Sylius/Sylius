<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PromotionTypeSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $checkerRegistry,
        ServiceRegistryInterface $actionRegistry
    ) {
        $this->beConstructedWith('Promotion', array('sylius'), $checkerRegistry, $actionRegistry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\PromotionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', 'text', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'text', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('exclusive', 'checkbox', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('usageLimit', 'integer', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('startsAt', 'date', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('endsAt', 'date', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('couponBased', 'checkbox', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('rules', 'sylius_promotion_rule_collection', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('actions', 'sylius_promotion_action_collection', Argument::type('array'))
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    function it_should_define_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'Promotion',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion');
    }
}
