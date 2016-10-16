<?php

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type\Action;

use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitFixedDiscountConfigurationType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\PromotionBundle\Form\Type\Filter\ActionFiltersType;

class UnitFixedDiscountConfigurationTypeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(UnitFixedDiscountConfigurationType::class);
    }

    public function it_should_be_an_fixed_discount_configuration_type()
    {
        $this->shouldHaveType(FixedDiscountConfigurationType::class);
    }

    public function it_should_have_filters(FormBuilderInterface $builder)
    {
        $builder
            ->add('amount', 'sylius_money', Argument::any())
            ->willReturn($builder);

        $builder
            ->add('filters', ActionFiltersType::class, Argument::withKey('empty_data'))
            ->willReturn($builder);

        $this->buildForm($builder, []);
    }
}
