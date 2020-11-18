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
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\GuestChangePaymentMethod;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class GuestChangePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ): void {
        $this->beConstructedWith($paymentMethodChanger, $orderRepository, $userContext);
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext
    ): void {
        $changePaymentMethod = new GuestChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $changePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $changePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $userContext->getUser()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePaymentMethod])
        ;
    }

    function it_throws_an_exception_if_user_is_not_null(
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        OrderInterface $order,
        ShopUserInterface $shopUser
    ): void {
        $changePaymentMethod = new GuestChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $changePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $changePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $userContext->getUser()->willReturn($shopUser);
        $order->getUser()->willReturn($shopUser);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePaymentMethod])
        ;
    }

    function it_assigns_guest_s_change_payment_method_to_specified_payment_after_checkout_completed(
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        UserContextInterface $userContext,
        OrderInterface $order
    ): void {
        $changePaymentMethod = new GuestChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $changePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $changePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $userContext->getUser()->willReturn(null);
        $order->getUser()->willReturn(null);

        $paymentMethodChanger
            ->changePaymentMethod(
                'CASH_ON_DELIVERY_METHOD',
                '123',
                $order
            )
            ->willReturn($order)
        ;

        $this($changePaymentMethod)->shouldReturn($order);
    }
}
