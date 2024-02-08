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
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendOrderConfirmationHandlerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($sender, $orderRepository);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_sends_order_confirmation_message(
        OrderInterface $order,
        SendOrderConfirmation $sendOrderConfirmation,
        SenderInterface $sender,
        CustomerInterface $customer,
        ChannelInterface $channel,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $sendOrderConfirmation->orderToken()->willReturn('TOKEN');

        $orderRepository->findOneByTokenValue('TOKEN')->willReturn($order);

        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('pl_PL');

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('johnny.bravo@email.com');

        $sender->send(
            Emails::ORDER_CONFIRMATION,
            ['johnny.bravo@email.com'],
            [
                'order' => $order->getWrappedObject(),
                'channel' => $channel->getWrappedObject(),
                'localeCode' => 'pl_PL',
            ],
        )->shouldBeCalled();

        $this(new SendOrderConfirmation('TOKEN'));
    }
}
