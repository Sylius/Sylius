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

namespace spec\Sylius\Bundle\CoreBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;

final class CustomerAndChannelBasedCartContextSpec extends ObjectBehavior
{
    function let(
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($customerContext, $channelContext, $orderRepository);
    }

    function it_implements_cart_context_interface(): void
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_uncompleted_cart_for_currently_logged_user(
        ChannelInterface $channel,
        ChannelContextInterface $channelContext,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $customerContext->getCustomer()->willReturn($customer);

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn($order);

        $this->getCart()->shouldReturn($order);
    }

    function it_throws_exception_if_no_cart_can_be_provided(
        ChannelInterface $channel,
        ChannelContextInterface $channelContext,
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository
    ): void {
        $channelContext->getChannel()->willReturn($channel);
        $customerContext->getCustomer()->willReturn($customer);

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $this
            ->shouldThrow(new CartNotFoundException('Sylius was not able to find the cart for currently logged in user.'))
            ->during('getCart', [])
        ;
    }

    function it_throws_exception_if_there_is_no_logged_in_customer(
        CustomerContextInterface $customerContext,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ): void {
        $customerContext->getCustomer()->willReturn(null);
        $channelContext->getChannel()->willReturn($channel);

        $this
            ->shouldThrow(new CartNotFoundException('Sylius was not able to find the cart, as there is no logged in user.'))
            ->during('getCart', [])
        ;
    }

    function it_does_nothing_if_channel_could_not_be_found(ChannelContextInterface $channelContext): void
    {
        $channelContext->getChannel()->willThrow(new ChannelNotFoundException());

        $this
            ->shouldThrow(new CartNotFoundException('Sylius was not able to find the cart, as there is no current channel.'))
            ->during('getCart', [])
        ;
    }
}
