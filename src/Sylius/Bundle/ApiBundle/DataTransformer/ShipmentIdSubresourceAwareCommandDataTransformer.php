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

namespace Sylius\Bundle\ApiBundle\DataTransformer;

use Sylius\Bundle\ApiBundle\Command\ShipmentIdSubresourceAwareInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShipmentIdSubresourceAwareCommandDataTransformer implements CommandDataTransformerInterface
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function transform($object, string $to, array $context = [])
    {
        /** @var ShipmentIdSubresourceAwareInterface $object */
        $attributes = $this->requestStack->getCurrentRequest()->attributes;

        Assert::true($attributes->has('shipmentId'), 'Path does not have shipment id');

        /** @var string $subresourceId */
        $shipmentId = $attributes->get('shipmentId');

        $object->setShipmentId($shipmentId);

        return $object;
    }

    public function supportsTransformation($object): bool
    {
        return $object instanceof ShipmentIdSubresourceAwareInterface;
    }
}
