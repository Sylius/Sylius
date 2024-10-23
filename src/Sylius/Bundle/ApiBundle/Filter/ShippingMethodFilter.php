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

namespace Sylius\Bundle\ApiBundle\Filter;

use ApiPlatform\Core\Api\FilterInterface;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    ShippingMethodFilter::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class ShippingMethodFilter implements FilterInterface
{
    public function getDescription(string $resourceClass): array
    {
        return [
            'shipmentId' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Shipment',
                    'description' => 'Id of shipment for which you would like to check available shipping methods',
                ],
            ],
            'tokenValue' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Order',
                    'description' => 'Token of an order for which you would like to check available shipping methods. Should be set if you are checking shipping methods for shipment, that is not assigned to user.',
                ],
            ],
        ];
    }
}
