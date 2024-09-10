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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Component\Core\Model\ShipmentInterface;

final class ShipmentIdAwareInputCommandDataTransformer // implements CommandDataTransformerInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function transform($object, string $to, array $context = [])
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $context['object_to_populate'];

        $object->setShipmentId($shipment->getId());

        return $object;
    }

    public function supportsTransformation($object): bool
    {
//        return $object instanceof ShipmentIdAwareInterface;
    }
}
