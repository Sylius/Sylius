<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type\Action;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FixedDiscountConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_should_build_form_with_count_field_and_equal_checkbox(FormBuilder $builder)
    {
        $builder
            ->add('amount', 'sylius_money', Argument::any())
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }
}
