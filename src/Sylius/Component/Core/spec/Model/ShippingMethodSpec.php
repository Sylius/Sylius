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
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethod as BaseShippingMethod;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

final class ShippingMethodSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethod::class);
    }

    function it_implements_a_shipping_method_interface()
    {
        $this->shouldImplement(ShippingMethodInterface::class);
    }

    function it_extends_a_shipping_method()
    {
        $this->shouldHaveType(BaseShippingMethod::class);
    }

    function it_does_not_have_any_zone_defined_by_default()
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_allows_defining_zone(ZoneInterface $zone)
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    function its_tax_category_is_mutable(TaxCategoryInterface $category)
    {
        $this->setTaxCategory($category);
        $this->getTaxCategory()->shouldReturn($category);
    }
}
