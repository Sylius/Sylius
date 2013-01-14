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
 * Flat rate per item calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PerItemRateCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\PerItemRateCalculator');
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
    function it_should_have_required_amount_configuration_options($resolver)
    {
        $resolver->setRequired(array('amount'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setAllowedTypes(array('amount' => array('numeric')))->shouldBeCalled()->willReturn($resolver);

        $this->setConfiguration($resolver);
    }

    function it_should_return_per_item_rate_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_shipping_calculator_per_item_rate_configuration');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Doctrine\Common\Collections\Collection                     $shippingItems
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_total_with_the_per_item_amount_configured_on_the_method($shipment, $shippingItems, $method)
    {
        $shipment->getMethod()->willReturn($method);
        $method->getConfiguration()->willReturn(array('amount' => 2.00));

        $shippingItems->count()->willReturn(11);
        $shipment->getItems()->willReturn($shippingItems);

        $this->calculate($shipment)->shouldReturn(22.00);
    }
}
