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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendOrderConfirmation;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendOrderConfirmationHandlerSpec extends ObjectBehavior
{
    function let(OrderEmailManagerInterface $orderEmailManager, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($orderEmailManager, $orderRepository);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_sends_order_confirmation_message(
        OrderEmailManagerInterface $orderEmailManager,
        OrderInterface $order,
        CustomerInterface $customer,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $orderRepository->findOneByTokenValue('TOKEN')->willReturn($order);

        $order->getLocaleCode()->willReturn('pl_PL');

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('johnny.bravo@email.com');

        $orderEmailManager->sendConfirmationEmail($order)->shouldBeCalled();

        $this(new SendOrderConfirmation('TOKEN'));
    }
}
