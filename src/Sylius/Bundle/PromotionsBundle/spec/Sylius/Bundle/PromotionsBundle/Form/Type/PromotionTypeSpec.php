<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionTypeSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistryInterface    $checkerRegistry
     * @param Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface $actionRegistry
     */
    function let($checkerRegistry, $actionRegistry)
    {
        $this->beConstructedWith('Promotion', array('sylius'), $checkerRegistry, $actionRegistry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\Type\PromotionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_proper_fields($builder, $checkerRegistry, $actionRegistry)
    {
        $builder
            ->add('name', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('usageLimit', 'integer', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('startsAt', 'date', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('endsAt', 'date', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('couponBased', 'checkbox', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('rules', 'collection', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('actions', 'collection', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $checkerRegistry->getCheckers()->willReturn(array());
        $actionRegistry->getActions()->willReturn(array());

        $builder->setAttribute('prototypes', Argument::any())->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
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
}
