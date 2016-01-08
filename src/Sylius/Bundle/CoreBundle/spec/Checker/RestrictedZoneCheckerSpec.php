<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

class RestrictedZoneCheckerSpec extends ObjectBehavior
{
    function let(CustomerContextInterface $customerContext, ZoneMatcherInterface $zoneMatcher)
    {
        $this->beConstructedWith($customerContext, $zoneMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checker\RestrictedZoneChecker');
    }

    function it_implements_Sylius_cart_item_resolver_interface()
    {
        $this->shouldImplement(RestrictedZoneCheckerInterface::class);
    }

    function it_is_not_restricted_if_customer_is_not_authenticated(ProductInterface $product, $customerContext)
    {
        $customerContext->getCustomer()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_customer_have_no_shipping_address(
        $customerContext,
        ProductInterface $product,
        CustomerInterface $customer
    ) {
        $customerContext->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getShippingAddress()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_product_have_no_restricted_zone(
        $customerContext,
        CustomerInterface $customer,
        AddressInterface $address,
        ProductInterface $product
    ) {
        $customerContext->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_zone_matcher_does_not_match_customers_shipping_address(
        $customerContext,
        $zoneMatcher,
        CustomerInterface $customer,
        AddressInterface $address,
        ProductInterface $product,
        ZoneInterface $zone
    ) {
        $customerContext->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn($zone);
        $zoneMatcher->matchAll($address)->shouldBeCalled()->willReturn([]);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_restricted_if_zone_matcher_match_customers_shipping_address(
        $customerContext,
        $zoneMatcher,
        CustomerInterface $customer,
        AddressInterface $address,
        ProductInterface $product,
        ZoneInterface $zone
    ) {
        $customerContext->getCustomer()->shouldBeCalled()->willReturn($customer);
        $customer->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn($zone);
        $zoneMatcher->matchAll($address)->shouldBeCalled()->willReturn([$zone]);

        $this->isRestricted($product)->shouldReturn(true);
    }
}
