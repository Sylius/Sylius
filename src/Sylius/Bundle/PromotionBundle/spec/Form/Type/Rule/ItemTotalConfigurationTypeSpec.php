<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type\Rule;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemTotalConfigurationTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array('sylius'));
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\Rule\ItemTotalConfigurationType');
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_should_build_form_with_amount_field_and_equals_checkbox(FormBuilder $builder)
    {
        $builder
            ->add('amount', 'sylius_money', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('equal', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }
}
