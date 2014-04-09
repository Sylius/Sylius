<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;

class ShippingMethodSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\ShippingMethod');
    }

    function it_should_implement_Sylius_core_shipping_method_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\ShippingMethodInterface');
    }

    function it_should_extend_Sylius_shipping_method_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Model\ShippingMethod');
    }

    function it_should_not_have_any_zone_defined_by_default()
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_should_allow_defining_zone(ZoneInterface $zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }
}
