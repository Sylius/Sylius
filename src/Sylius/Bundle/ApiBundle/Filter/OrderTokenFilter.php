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

final class OrderTokenFilter implements FilterInterface
{
    /** @return array<string, array{tokenValue: array{type: string, required: false, property: null, swagger: array{ name: string, description: string }}}> */
    public function getDescription(string $resourceClass): array
    {
        return [
            'tokenValue' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Order',
                    'description' => 'Token value of order for which you would like to get adjustments.',
                ],
            ],
        ];
    }
}
