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

namespace Sylius\Bundle\CoreBundle\Telemetry\DTO\Business;

use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class MetricsCountsData implements TelemetryDataInterface
{
    public function __construct(
        public string $customersCount,
        public string $productsCount,
        public string $productVariantsCount,
        public string $virtualProductVariantsCount,
        public int $ordersCount,
        public int $channelsCount,
    ) {
    }

    public function normalize(): array
    {
        return [
            'customers_count' => $this->customersCount,
            'products_count' => $this->productsCount,
            'product_variants_count' => $this->productVariantsCount,
            'virtual_product_variants_count' => $this->virtualProductVariantsCount,
            'orders_count' => $this->ordersCount,
            'channels_count' => $this->channelsCount,
        ];
    }
}
