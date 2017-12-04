<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;

final class UserImpersonatedListenerSpec extends ObjectBehavior
{
    function let(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($cartStorage, $channelContext, $orderRepository);
    }

    function it_sets_cart_id_of_an_impersonated_customer_in_session(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository,
        UserEvent $event,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ChannelInterface $channel,
        OrderInterface $cart
    ): void {
        $event->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn($cart);

        $cartStorage->setForChannel($channel, $cart)->shouldBeCalled();

        $this->onUserImpersonated($event);
    }

    function it_removes_the_current_cart_id_if_an_impersonated_customer_has_no_cart(
        CartStorageInterface $cartStorage,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository,
        UserEvent $event,
        ShopUserInterface $user,
        CustomerInterface $customer,
        ChannelInterface $channel
    ): void {
        $event->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);

        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $cartStorage->removeForChannel($channel)->shouldBeCalled();

        $this->onUserImpersonated($event);
    }
}
