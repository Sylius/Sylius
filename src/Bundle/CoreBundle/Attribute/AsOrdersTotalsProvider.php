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

namespace Sylius\Bundle\CoreBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class AsOrdersTotalsProvider
{
    public const SERVICE_TAG = 'sylius.statistics.orders_totals_provider';

    /**
     * @example #[AsOrdersTotalsProvider('week')]
     */
    public function __construct(
        private string $type,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
