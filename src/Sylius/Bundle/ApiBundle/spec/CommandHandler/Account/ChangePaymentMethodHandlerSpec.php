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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Account\ChangePaymentMethod;
use Sylius\Bundle\ApiBundle\CommandHandler\Changer\CommandPaymentMethodChangerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class ChangePaymentMethodHandlerSpec extends ObjectBehavior
{
    function let(
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository
    ): void {
        $this->beConstructedWith($commandPaymentMethodChanger, $orderRepository);
    }

    function it_throws_an_exception_if_order_with_given_token_has_not_been_found(
        OrderRepositoryInterface $orderRepository,
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger
    ): void {
        $changePaymentMethod = new ChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $changePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $changePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $commandPaymentMethodChanger
            ->changePaymentMethod($changePaymentMethod, Argument::type(OrderInterface::class))
            ->shouldNotBeCalled()
        ;

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$changePaymentMethod])
        ;
    }

    function it_assigns_guest_s_change_payment_method_to_specified_payment_after_checkout_completed(
        CommandPaymentMethodChangerInterface $commandPaymentMethodChanger,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ): void {
        $changePaymentMethod = new ChangePaymentMethod('CASH_ON_DELIVERY_METHOD');
        $changePaymentMethod->setOrderTokenValue('ORDERTOKEN');
        $changePaymentMethod->setSubresourceId('123');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $commandPaymentMethodChanger->changePaymentMethod($changePaymentMethod, $order)->willReturn($order);

        $this($changePaymentMethod)->shouldReturn($order);
    }
}
