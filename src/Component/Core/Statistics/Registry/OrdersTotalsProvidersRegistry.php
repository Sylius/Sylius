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

use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;
use Webmozart\Assert\Assert;

final class OrdersTotalsProvidersRegistry implements OrdersTotalsProviderRegistryInterface
{
    /** @var array<string, OrdersTotalsProviderInterface> */
    private array $ordersTotalProviders;

    /**
     * @param \Traversable<string, OrdersTotalsProviderInterface> $ordersTotalsProviders
     */
    public function __construct(\Traversable $ordersTotalsProviders)
    {
        $this->ordersTotalProviders = iterator_to_array($ordersTotalsProviders);
    }

    public function getByType(string $type): OrdersTotalsProviderInterface
    {
        Assert::keyExists($this->ordersTotalProviders, $type);

        return $this->ordersTotalProviders[$type];
    }
}
