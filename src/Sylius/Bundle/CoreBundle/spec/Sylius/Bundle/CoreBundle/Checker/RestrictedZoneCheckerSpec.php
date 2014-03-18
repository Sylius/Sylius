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
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class RestrictedZoneCheckerSpec extends ObjectBehavior
{
    function let(SecurityContextInterface $securityContext, ZoneMatcherInterface $zoneMatcher)
    {
        $this->beConstructedWith($securityContext, $zoneMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checker\RestrictedZoneChecker');
    }

    function it_implements_Sylius_cart_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface');
    }

    function it_is_not_restricted_if_user_is_not_authenticated(ProductInterface $product, $securityContext)
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(false);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_user_have_no_shipping_address(
        ProductInterface $product,
        $securityContext,
        TokenInterface $token,
        UserInterface $user
    )
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_product_have_no_restricted_zone(
        ProductInterface $product,
        $securityContext,
        TokenInterface $token,
        UserInterface $user,
        AddressInterface $address,
        ProductInterface $product
    )
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_not_restricted_if_zone_matcher_does_not_match_users_shipping_address(
        ProductInterface $product,
        $securityContext,
        $zoneMatcher,
        TokenInterface $token,
        UserInterface $user,
        AddressInterface $address,
        ProductInterface $product,
        ZoneInterface $zone
    )
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn($zone);
        $zoneMatcher->matchAll($address)->shouldBeCalled()->willReturn(array());

        $this->isRestricted($product)->shouldReturn(false);
    }

    function it_is_restricted_if_zone_matcher_match_users_shipping_address(
        ProductInterface $product,
        $securityContext,
        $zoneMatcher,
        TokenInterface $token,
        UserInterface$user,
        AddressInterface $address,
        ProductInterface $product,
        ZoneInterface $zone
    )
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn($zone);
        $zoneMatcher->matchAll($address)->shouldBeCalled()->willReturn(array($zone));

        $this->isRestricted($product)->shouldReturn(true);
    }
}
