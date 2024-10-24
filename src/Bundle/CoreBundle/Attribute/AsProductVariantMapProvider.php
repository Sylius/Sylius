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
final class AsProductVariantMapProvider
{
    public const SERVICE_TAG = 'sylius.product_variant_data_map_provider';

    public function __construct(
        private int $priority = 0,
    ) {
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
