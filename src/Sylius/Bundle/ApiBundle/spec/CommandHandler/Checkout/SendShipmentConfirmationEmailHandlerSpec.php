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
use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendShipmentConfirmationEmailHandlerSpec extends ObjectBehavior
{
    function let(SenderInterface $sender, ShipmentRepositoryInterface $shipmentRepository): void
    {
        $this->beConstructedWith($sender, $shipmentRepository);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_sends_shipment_confirmation_message(
        ShipmentInterface $shipment,
        SenderInterface $sender,
        CustomerInterface $customer,
        ChannelInterface $channel,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderInterface $order,
    ): void {
        $shipmentRepository->find(123)->willReturn($shipment);
        $shipment->getOrder()->willReturn($order);

        $order->getChannel()->willReturn($channel);
        $order->getLocaleCode()->willReturn('pl_PL');

        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('johnny.bravo@email.com');

        $sender->send(
            Emails::SHIPMENT_CONFIRMATION,
            ['johnny.bravo@email.com'],
            [
                'shipment' => $shipment->getWrappedObject(),
                'order' => $order->getWrappedObject(),
                'channel' => $channel->getWrappedObject(),
                'localeCode' => 'pl_PL',
            ],
        )->shouldBeCalled();

        $this(new SendShipmentConfirmationEmail(123));
    }
}
