<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Model;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\RuleInterface;

/**
 * Shipping method rule model spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class Rule extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\Rule');
    }

    function it_should_be_Sylius_promotion_rule()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\RuleInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_should_be_mutable()
    {
        $this->setType(RuleInterface::TYPE_ITEM_COUNT);
        $this->getType()->shouldReturn(RuleInterface::TYPE_ITEM_COUNT);
    }

    function it_should_initialize_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn(array());
    }

    function its_configuration_should_be_mutable()
    {
        $this->setConfiguration(array('value' => 500));
        $this->getConfiguration()->shouldReturn(array('value' => 500));
    }

    function it_should_not_have_shipping_method_by_default()
    {
        $this->getShippingMethod()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $shippingMethod
     */
    function its_shipping_method_by_should_be_mutable($shippingMethod)
    {
        $this->setShippingMethod($shippingMethod);
        $this->getShippingMethod()->shouldReturn($shippingMethod);
    }
}
