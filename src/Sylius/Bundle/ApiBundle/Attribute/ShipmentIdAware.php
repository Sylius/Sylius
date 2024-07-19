<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class ShipmentIdAware
{
    public function __construct(public string $constructorArgumentName = 'shipmentId')
    {
    }
}
