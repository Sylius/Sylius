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

class RestrictedZoneCheckerSpec extends ObjectBehavior
{
    /**
     * @param Symfony\Component\Security\Core\SecurityContextInterface    $securityContext
     * @param Sylius\Bundle\AddressingBundle\Matcher\ZoneMatcherInterface $zoneMatcher
     */
    function let($securityContext, $zoneMatcher)
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

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface $product
     */
    function it_is_not_restricted_if_user_is_not_authenticated($product, $securityContext)
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(false);

        $this->isRestricted($product)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface                     $product
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param Sylius\Bundle\CoreBundle\Model\UserInterface                        $user
     */
    function it_is_not_restricted_if_user_have_no_shipping_address($product, $securityContext, $token, $user)
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface                     $product
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param Sylius\Bundle\CoreBundle\Model\UserInterface                        $user
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface               $address
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface                     $product
     */
    function it_is_not_restricted_if_product_have_no_restricted_zone($product, $securityContext, $token, $user, $address, $product)
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn(null);

        $this->isRestricted($product)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface                     $product
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param Sylius\Bundle\CoreBundle\Model\UserInterface                        $user
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface               $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface                  $zone
     */
    function it_is_not_restricted_if_zone_matcher_does_not_match_users_shipping_address($product, $securityContext, $zoneMatcher, $token, $user, $address, $product, $zone)
    {
        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->shouldBeCalled()->willReturn(true);
        $securityContext->getToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);
        $user->getShippingAddress()->shouldBeCalled()->willReturn($address);
        $product->getRestrictedZone()->shouldBeCalled()->willReturn($zone);
        $zoneMatcher->matchAll($address)->shouldBeCalled()->willReturn(array());

        $this->isRestricted($product)->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface                     $product
     * @param Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param Sylius\Bundle\CoreBundle\Model\UserInterface                        $user
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface               $address
     * @param Sylius\Bundle\AddressingBundle\Model\ZoneInterface                  $zone
     */
    function it_is_restricted_if_zone_matcher_match_users_shipping_address($product, $securityContext, $zoneMatcher, $token, $user, $address, $product, $zone)
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
