<?php

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use PHPSpec2\ObjectBehavior;

/**
 * Delegating shipping charge calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingShippingChargeCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\DelegatingShippingChargeCalculator');
    }

    function it_should_be_a_Sylius_shipping_charge_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface');
    }

    function it_should_initialize_calculators_array_by_default()
    {
        $this->getCalculators()->shouldReturn(array());
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface $calculator
     */
    function it_should_register_calculator_properly($calculator)
    {
        $this->hasCalculator('default')->shouldReturn(false);
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface $calculator
     */
    function it_should_unregister_calculator_properly($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);

        $this->unregisterCalculator('default');
        $this->hasCalculator('default')->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface $calculator
     */
    function it_should_retrieve_registered_calculator_by_name($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->getCalculator('default')->shouldReturn($calculator);
    }

    function it_should_complain_if_trying_to_retrieve_non_existing_calculator()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringGetCalculator('default')
        ;
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_complain_if_shipment_has_no_method_defined($shipment)
    {
        $shipment->getMethod()->willReturn(null);

        $this
            ->shouldThrow('LogicException')
            ->duringCalculate($shipment)
        ;
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface                      $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface                $method
     * @param Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface $calculator
     */
    function it_should_delegate_calculation_to_a_correct_calculator($shipment, $method, $calculator)
    {
        $this->registerCalculator('default', $calculator);
        $shipment->getMethod()->willReturn($method);
        $method->getCalculator()->willReturn('default');

        $calculator->calculate($shipment)->shouldBeCalled()->willReturn(10);

        $this->calculate($shipment)->shouldReturn(10);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface                      $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface                $method
     * @param Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface $calculator
     */
    function it_should_complain_if_correct_calculator_doesnt_exist_for_given_method($shipment, $method, $calculator)
    {
        $this->registerCalculator('default', $calculator);
        $shipment->getMethod()->willReturn($method);
        $method->getCalculator()->willReturn('custom');

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCalculate($shipment)
        ;
    }
}
