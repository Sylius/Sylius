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

namespace Sylius\Component\Core\Statistics\Registry;

use Sylius\Component\Core\Statistics\Provider\OrdersCount\OrdersCountProviderInterface;
use Webmozart\Assert\Assert;

final class OrdersCountProviderRegistry implements OrdersCountProviderRegistryInterface, StatisticsProviderRegistryInterface
{
    /** @var array<string, OrdersCountProviderInterface> */
    private array $ordersCountProviders;

    /** @param \Traversable<string, OrdersCountProviderInterface> $ordersCountProviders */
    public function __construct(\Traversable $ordersCountProviders)
    {
        $this->ordersCountProviders = iterator_to_array($ordersCountProviders);
    }

    public function getByType(string $type): OrdersCountProviderInterface
    {
        Assert::keyExists($this->ordersCountProviders, $type);

        return $this->ordersCountProviders[$type];
    }
}
