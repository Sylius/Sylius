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
    /** @var string */
    public $customersCount;

    /** @var string */
    public $productsCount;

    /** @var string */
    public $productVariantsCount;

    /** @var int */
    public $ordersCount;

    public function __construct(
        string $customersCount,
        string $productsCount,
        string $productVariantsCount,
        int $ordersCount
    ) {
        $this->customersCount = $customersCount;
        $this->productsCount = $productsCount;
        $this->productVariantsCount = $productVariantsCount;
        $this->ordersCount = $ordersCount;
    }

    public function normalize(): array
    {
        return [
            'customers_count' => $this->customersCount,
            'products_count' => $this->productsCount,
            'product_variants_count' => $this->productVariantsCount,
            'orders_count' => $this->ordersCount,
        ];
    }
}
