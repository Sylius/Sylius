<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Security;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface;
use Sylius\Bundle\ApiBundle\Security\OrderAdjustmentsVoter;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class OrderAdjustmentsVoterSpec extends ObjectBehavior
{
    function let(AdjustmentOrderProviderInterface $adjustmentOrderProvider): void
    {
        $this->beConstructedWith($adjustmentOrderProvider);
    }

    function it_only_supports_sylius_order_adjustment_attribute(): void
    {
        $this->supportsAttribute(OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT)->shouldReturn(true);
        $this->supportsAttribute('OTHER_ATTRIBUTE')->shouldReturn(false);
    }

    function it_votes_granted_when_collection_is_empty(TokenInterface $token): void
    {
        $collection = new ArrayCollection();

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_granted_when_order_user_matches_token_user(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $user = new ShopUser();
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);

        $token->getUser()->willReturn($user);
        $adjustmentOrderProvider->provide($adjustment)->willReturn($order);
        $order->getUser()->willReturn($user);
        $order->isCreatedByGuest()->willReturn(false);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_denied_when_order_user_does_not_match_token_user(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $tokenUser = new ShopUser();
        $orderUser = new ShopUser();
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);

        $token->getUser()->willReturn($tokenUser);
        $adjustmentOrderProvider->provide($adjustment)->willReturn($order);
        $order->getUser()->willReturn($orderUser);
        $order->isCreatedByGuest()->willReturn(false);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_votes_granted_when_both_order_and_token_have_no_user(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);

        $token->getUser()->willReturn(null);
        $adjustmentOrderProvider->provide($adjustment)->willReturn($order);
        $order->getUser()->willReturn(null);
        $order->isCreatedByGuest()->willReturn(true);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_granted_when_adjustment_has_no_order(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);

        $token->getUser()->willReturn(null);
        $adjustmentOrderProvider->provide($adjustment)->willReturn(null);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_granted_when_adjustment_has_order_created_by_guest_and_token_has_user(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);
        $user = new ShopUser();

        $token->getUser()->willReturn($user);
        $adjustmentOrderProvider->provide($adjustment)->willReturn($order);
        $order->getUser()->willReturn(null);
        $order->isCreatedByGuest()->willReturn(true);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_denied_when_order_has_user_and_token_has_no_user(
        TokenInterface $token,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        AdjustmentOrderProviderInterface $adjustmentOrderProvider,
    ): void {
        $collection = new ArrayCollection([$adjustment->getWrappedObject()]);
        $user = new ShopUser();

        $token->getUser()->willReturn(null);
        $adjustmentOrderProvider->provide($adjustment)->willReturn($order);
        $order->getUser()->willReturn($user);
        $order->isCreatedByGuest()->willReturn(true);

        $this->vote($token, $collection, [OrderAdjustmentsVoter::SYLIUS_ORDER_ADJUSTMENT])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }
}
