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

use PHPSpec2\ObjectBehavior;

/**
 * Flexible rate calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FlexibleRateCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\FlexibleRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface');
    }

    function it_should_be_configurable()
    {
        $this->shouldBeConfigurable();
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_have_required_first_and_additional_items_cost_with_limit_configuration_options($resolver)
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

    function it_should_return_flexible_rate_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_calculator_flexible_rate_configuration');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Doctrine\Common\Collections\Collection                     $shippingItems
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_first_item_cost_if_shipment_has_only_one_item($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1000,
            'additional_item_cost'  => 200,
            'additional_item_limit' => 0
        );

        $shipment->getMethod()->willReturn($method);
        $method->getConfiguration()->willReturn($configuration);

        $shippingItems->count()->willReturn(1);
        $shipment->getItems()->willReturn($shippingItems);

        $this->calculate($shipment)->shouldReturn(1000);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Doctrine\Common\Collections\Collection                     $shippingItems
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_first_and_every_additional_item_cost_when_shipment_has_more_items($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1500,
            'additional_item_cost'  => 300,
            'additional_item_limit' => 0
        );

        $shipment->getMethod()->willReturn($method);
        $method->getConfiguration()->willReturn($configuration);

        $shippingItems->count()->willReturn(5);
        $shipment->getItems()->willReturn($shippingItems);

        $this->calculate($shipment)->shouldReturn(2700);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Doctrine\Common\Collections\Collection                     $shippingItems
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_first_and_every_additional_item_cost_taking_limit_into_account($shipment, $shippingItems, $method)
    {
        $configuration = array(
            'first_item_cost'       => 1500,
            'additional_item_cost'  => 300,
            'additional_item_limit' => 3
        );

        $shipment->getMethod()->willReturn($method);
        $method->getConfiguration()->willReturn($configuration);

        $shippingItems->count()->willReturn(8);
        $shipment->getItems()->willReturn($shippingItems);

        $this->calculate($shipment)->shouldReturn(2400);
    }
}
