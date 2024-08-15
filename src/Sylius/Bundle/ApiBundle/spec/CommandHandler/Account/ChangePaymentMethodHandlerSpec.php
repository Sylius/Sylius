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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class ChangePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $this->beConstructedWith($paymentMethodChanger, $orderRepository);
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodChangerInterface $paymentMethodChanger,
    ): void {
        $changePaymentMethod = new ChangePaymentMethod('CASH_ON_DELIVERY_METHOD', 123, 'ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $paymentMethodChanger
            ->changePaymentMethod(
                'CASH_ON_DELIVERY_METHOD',
                123,
                Argument::type(OrderInterface::class),
            )
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePaymentMethod])
        ;
    }

    function it_assigns_shop_user_s_change_payment_method_to_specified_payment_after_checkout_completed(
        PaymentMethodChangerInterface $paymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $changePaymentMethod = new ChangePaymentMethod('CASH_ON_DELIVERY_METHOD', 123, 'ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $paymentMethodChanger
            ->changePaymentMethod(
                'CASH_ON_DELIVERY_METHOD',
                123,
                $order,
            )
            ->willReturn($order)
        ;

        $this($changePaymentMethod)->shouldReturn($order);
    }
}
