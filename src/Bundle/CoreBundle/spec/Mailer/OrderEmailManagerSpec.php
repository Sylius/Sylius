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

namespace spec\Sylius\Bundle\CoreBundle\Mailer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class OrderEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_an_order_email_manager_interface(): void
    {
        $this->shouldImplement(OrderEmailManagerInterface::class);
    }

    function it_sends_an_order_confirmation_email(
        SenderInterface $sender,
        OrderInterface $order,
        ChannelInterface $channel,
        CustomerInterface $customer,
    ): void {
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('customer@example.com');

        $sender
            ->send('order_confirmation', ['customer@example.com'], [
                'order' => $order,
                'channel' => $channel,
                'localeCode' => 'en_US',
            ])
            ->shouldBeCalled()
        ;

        $this->sendConfirmationEmail($order);
    }
}
