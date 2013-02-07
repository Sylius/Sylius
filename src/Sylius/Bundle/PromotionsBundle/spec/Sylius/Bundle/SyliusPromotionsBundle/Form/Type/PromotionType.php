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

use PHPSpec2\ObjectBehavior;

/**
 * Promotion form type spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class PromotionType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Promotion');
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
    function it_should_build_form_with_proper_fields($builder)
    {
        // $builder->addEventSubscriber(ANY_ARGUMENT)->willReturn($builder);

        $builder
            ->add('name', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('code', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('usageLimit', 'integer', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('startsAt', 'date', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('endsAt', 'date', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('rule', 'sylius_promotion_rule', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('action', 'sylius_promotion_action', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'Promotion'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
