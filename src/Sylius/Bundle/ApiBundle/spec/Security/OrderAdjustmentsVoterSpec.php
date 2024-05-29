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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class OrderAdjustmentsVoterSpec extends ObjectBehavior
{
    function it_does_not_support_other_attributes(): void
    {
        $this->supportsAttribute('OTHER_ATTRIBUTE')->shouldReturn(false);
    }

    function it_votes_granted_when_collection_is_empty(TokenInterface $token): void
    {
        $collection = new ArrayCollection();

        $this->vote($token, $collection, ['SYLIUS_ORDER_ADJUSTMENT'])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_granted_when_order_user_matches_token_user(
        TokenInterface $token,
        Collection $collection,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
    ): void {
        $user = new ShopUser();

        $token->getUser()->willReturn($user);
        $collection->isEmpty()->willReturn(false);
        $collection->first()->willReturn($adjustment);
        $adjustment->getOrder()->willReturn($order);
        $order->getUser()->willReturn($user);
        $order->isCreatedByGuest()->willReturn(false);

        $this->vote($token, $collection, ['SYLIUS_ORDER_ADJUSTMENT'])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_denied_when_order_user_does_not_match_token_user(
        TokenInterface $token,
        Collection $collection,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
    ): void {
        $tokenUser = new ShopUser();
        $orderUser = new ShopUser();

        $token->getUser()->willReturn($tokenUser);
        $collection->isEmpty()->willReturn(false);
        $collection->first()->willReturn($adjustment);
        $adjustment->getOrder()->willReturn($order);
        $order->getUser()->willReturn($orderUser);
        $order->isCreatedByGuest()->willReturn(false);

        $this->vote($token, $collection, ['SYLIUS_ORDER_ADJUSTMENT'])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }

    function it_votes_granted_for_order(
        TokenInterface $token,
        Collection $collection,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
    ): void {
        $token->getUser()->willReturn(null);
        $collection->isEmpty()->willReturn(false);
        $collection->first()->willReturn($adjustment);
        $adjustment->getOrder()->willReturn($order);
        $order->getUser()->willReturn(null);
        $order->isCreatedByGuest()->willReturn(true);

        $this->vote($token, $collection, ['SYLIUS_ORDER_ADJUSTMENT'])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_granted_for_order_items(
        TokenInterface $token,
        Collection $collection,
        AdjustmentInterface $adjustment,
        OrderInterface $order,
        OrderItemInterface $orderItem,
    ): void {
        $user = new ShopUser();

        $token->getUser()->willReturn($user);
        $collection->isEmpty()->willReturn(false);
        $collection->first()->willReturn($adjustment);
        $adjustment->getOrder()->willReturn(null);
        $adjustment->getOrderItem()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);
        $order->getUser()->willReturn($user);
        $order->isCreatedByGuest()->willReturn(false);

        $this->vote($token, $collection, ['SYLIUS_ORDER_ADJUSTMENT'])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }
}
