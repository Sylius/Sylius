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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\ShipmentIdAwareInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

final class ShipmentIdAwareInputCommandDataTransformerSpec extends ObjectBehavior
{
    function it_supports_only_shipment_id_aware_interface(
        LocaleCodeAwareInterface $localeCodeAware,
        ShipmentIdAwareInterface $shipmentIdAware,
    ): void {
        $this->supportsTransformation($localeCodeAware)->shouldReturn(false);
        $this->supportsTransformation($shipmentIdAware)->shouldReturn(true);
    }

    function it_adds_shipment_id_to_object(
        ShipmentIdAwareInterface $command,
        ShipmentInterface $shipment,
    ): void {
        $context = ['object_to_populate' => $shipment];
        $shipment->getId()->willReturn(123);

        $command->setShipmentId(123);

        $this->transform($command, '', $context)->shouldReturn($command);
    }
}
