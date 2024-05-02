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

namespace spec\Sylius\Bundle\CoreBundle\MessageHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Message\ResendShipmentConfirmationEmail;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResendShipmentConfirmationEmailHandlerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $shipmentRepository, ShipmentEmailManagerInterface $shipmentEmailManager): void
    {
        $this->beConstructedWith($shipmentRepository, $shipmentEmailManager);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_resends_shipment_confirmation_email(
        ShipmentEmailManagerInterface $shipmentEmailManager,
        RepositoryInterface $shipmentRepository,
        ShipmentInterface $shipment,
    ): void {
        $shipmentRepository->find('12')->willReturn($shipment);
        $shipmentEmailManager->resendConfirmationEmail($shipment)->shouldBeCalled();

        $this->__invoke(new ResendShipmentConfirmationEmail(12));
    }

    function it_throws_not_found_exception_when_shipment_not_found(
        RepositoryInterface $shipmentRepository,
    ): void {
        $shipmentRepository->find('10')->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('__invoke', [new ResendShipmentConfirmationEmail(10)])
        ;
    }
}
