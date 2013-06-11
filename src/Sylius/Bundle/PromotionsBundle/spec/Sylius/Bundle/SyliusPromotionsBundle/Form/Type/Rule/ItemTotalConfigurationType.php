<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Form\Type\Rule;

use PHPSpec2\ObjectBehavior;

/**
 * Item total rule configuration form type spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalConfigurationType extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('sylius'));
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\Type\Rule\ItemTotalConfigurationType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_amount_field_and_equals_checkbox($builder)
    {
        $builder
            ->add('amount', 'sylius_money', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('equal', 'checkbox', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
