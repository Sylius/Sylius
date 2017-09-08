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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class UserImpersonatedListenerSpec extends ObjectBehavior
{
    function let(
        SessionInterface $session,
        ChannelContextInterface $channelContext,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($session, 'session_key_name', $channelContext, $orderRepository);
    }

    function it_sets_cart_id_of_an_impersonated_customer_in_session(
        SessionInterface $session,
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
        $channel->getCode()->willReturn('channel_code');

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn($cart);
        $cart->getId()->willReturn(14);

        $session->set('session_key_name.channel_code', 14)->shouldBeCalled();

        $this->userImpersonated($event);
    }

    function it_removes_the_current_cart_id_if_an_impersonated_customer_has_no_cart(
        SessionInterface $session,
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
        $channel->getCode()->willReturn('channel_code');

        $orderRepository->findLatestCartByChannelAndCustomer($channel, $customer)->willReturn(null);

        $session->remove('session_key_name.channel_code')->shouldBeCalled();

        $this->userImpersonated($event);
    }
}
