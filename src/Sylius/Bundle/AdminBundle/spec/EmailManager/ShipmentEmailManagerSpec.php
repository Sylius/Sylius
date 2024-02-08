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

namespace spec\Sylius\Bundle\AdminBundle\EmailManager;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;

final class ShipmentEmailManagerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender): void
    {
        $this->beConstructedWith($sender);
    }

    function it_implements_a_shipment_email_manager_interface(): void
    {
        $this->shouldImplement(ShipmentEmailManagerInterface::class);
    }

    function it_sends_a_shipment_confirmation_email(
        SenderInterface $sender,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        CustomerInterface $customer,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('en_US');
        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('customer@example.com');

        $sender
            ->send('shipment_confirmation', ['customer@example.com'], [
                'shipment' => $shipment,
                'order' => $order,
                'channel' => $channel,
                'localeCode' => 'en_US',
            ])
            ->shouldBeCalled()
        ;

        $this->sendConfirmationEmail($shipment);
    }
}
