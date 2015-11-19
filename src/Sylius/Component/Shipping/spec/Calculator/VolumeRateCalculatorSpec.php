<?php

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

class VolumeRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\VolumeRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Calculator\CalculatorInterface');
    }

    function it_is_configurable()
    {
        $this->shouldBeConfigurable();
    }

    function it_returns_volume_rate_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_calculator_volume_rate_configuration');
    }

    function it_should_calculate_the_flat_rate_amount_configured_on_the_method(ShippingSubjectInterface $subject)
    {
        $subject->getShippingVolume()->willReturn(100);

        $this->calculate($subject, array('amount' => 500, 'division' => 2))->shouldReturn(500 * 100/2);
    }

    function its_calculated_value_should_be_an_integer(ShippingSubjectInterface $subject)
    {
        $subject->getShippingVolume()->willReturn(100);

        $this->calculate($subject, array('amount' => 500, 'division' => 2))->shouldBeInteger();
    }
}
