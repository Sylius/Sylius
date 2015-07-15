<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\RuleInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Model\Rule');
    }

    public function it_implements_Sylius_shipping_method_rule_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\RuleInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_type_by_default()
    {
        $this->getType()->shouldReturn(null);
    }

    public function its_type_is_mutable()
    {
        $this->setType(RuleInterface::TYPE_ITEM_COUNT);
        $this->getType()->shouldReturn(RuleInterface::TYPE_ITEM_COUNT);
    }

    public function it_initializes_empty_array_for_configuration_by_default()
    {
        $this->getConfiguration()->shouldReturn(array());
    }

    public function its_configuration_is_mutable()
    {
        $this->setConfiguration(array('value' => 500));
        $this->getConfiguration()->shouldReturn(array('value' => 500));
    }

    public function it_does_not_belong_to_a_shipping_method_by_default()
    {
        $this->getMethod()->shouldReturn(null);
    }

    public function it_allows_to_assign_itself_to_a_shipping_method(ShippingMethodInterface $method)
    {
        $this->setMethod($method);
        $this->getMethod()->shouldReturn($method);
    }

    public function it_allows_to_detach_itself_from_a_shipping_method(ShippingMethodInterface $method)
    {
        $this->setMethod($method);
        $this->getMethod()->shouldReturn($method);

        $this->setMethod(null);
        $this->getMethod()->shouldReturn(null);
    }
}
