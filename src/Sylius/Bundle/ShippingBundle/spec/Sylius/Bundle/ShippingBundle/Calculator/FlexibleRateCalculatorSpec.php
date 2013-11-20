<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FlexibleRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\FlexibleRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface');
    }

    function it_is_configurable()
    {
        $this->shouldBeConfigurable();
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_has_required_first_and_additional_items_cost_with_limit_configuration_options($resolver)
    {
        $resolver->setDefaults(array('additional_item_limit' => 0))->shouldBeCalled()->willReturn($resolver);
        $resolver->setRequired(array('first_item_cost', 'additional_item_cost'))->shouldBeCalled()->willReturn($resolver);

        $resolver
            ->setAllowedTypes(array(
                'first_item_cost'       => array('numeric'),
                'additional_item_cost'  => array('numeric'),
                'additional_item_limit' => array('integer')
            ))
            ->shouldBeCalled()->willReturn($resolver)
        ;

        $this->setConfiguration($resolver);
    }

    function it_returns_flexible_rate_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_calculator_flexible_rate_configuration');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_calculate_the_first_item_cost_if_shipment_has_only_one_item($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1000,
            'additional_item_cost'  => 200,
            'additional_item_limit' => 0
        );

        $shipment->getShippingItemCount()->willReturn(1);

        $this->calculate($shipment, $configuration)->shouldReturn(1000);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_calculate_the_first_and_every_additional_item_cost_when_shipment_has_more_items($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1500,
            'additional_item_cost'  => 300,
            'additional_item_limit' => 0
        );

        $shipment->getShippingItemCount()->willReturn(5);

        $this->calculate($shipment, $configuration)->shouldReturn(2700);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     */
    function it_should_calculate_the_first_and_every_additional_item_cost_taking_limit_into_account($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1500,
            'additional_item_cost'  => 300,
            'additional_item_limit' => 3
        );

        $shipment->getShippingItemCount()->willReturn(8);

        $this->calculate($shipment, $configuration)->shouldReturn(2400);
    }
}
