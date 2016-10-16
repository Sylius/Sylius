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

use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitPercentageDiscountConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\PromotionBundle\Form\Type\Filter\ActionFiltersType;

/**
 * @author Viorel Craescu <viorel@craescu.com>
 * @author Gabi Udrescu <gabriel.udr@gmail.com>
 */
class UnitPercentageDiscountConfigurationTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(UnitPercentageDiscountConfigurationType::class);
    }

    public function it_is_a_percentage_discount_configuration_form_type()
    {
        $this->shouldHaveType(PercentageDiscountConfigurationType::class);
    }

    public function it_should_have_filters(FormBuilderInterface $builder)
    {
        $builder
            ->add('percentage', 'percent', Argument::any())
            ->willReturn($builder);

        $builder
            ->add('filters', ActionFiltersType::class, Argument::withKey('empty_data'))
            ->willReturn($builder);

        $this->buildForm($builder, []);
    }

    public function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_action_unit_percentage_discount_configuration');
    }
}
