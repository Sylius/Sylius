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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\GuestSChangePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Changer\CommandPaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class GuestSChangePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ): void {
        $this->beConstructedWith($commandPaymentMethodChanger, $orderRepository, $userContext);
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ): void {
        $guestSChangePaymentMethod = new GuestSChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $guestSChangePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $guestSChangePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $userContext->getUser()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$guestSChangePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_user_is_not_null(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        OrderInterface $order,
        ShopUserInterface $shopUser
    ): void {
        $guestSChangePaymentMethod = new GuestSChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $guestSChangePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $guestSChangePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $userContext->getUser()->willReturn($shopUser);
        $order->getUser()->willReturn($shopUser);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$guestSChangePaymentMethod])
        ;
    }

    function it_assigns_guest_s_change_payment_method_to_specified_payment_after_checkout_completed(
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        OrderInterface $order
    ): void {
        $guestSChangePaymentMethod = new GuestSChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $guestSChangePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $guestSChangePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $userContext->getUser()->willReturn(null);
        $order->getUser()->willReturn(null);

        $commandPaymentMethodChanger->changePaymentMethod($guestSChangePaymentMethod, $order)->willReturn($order);

        $this($guestSChangePaymentMethod)->shouldReturn($order);
    }
}
