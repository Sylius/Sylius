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

namespace Sylius\Component\Shipping\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @template T of ShippingMethodInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ShippingMethodRepositoryInterface extends RepositoryInterface
{
    /**
     * @return ShippingMethodInterface[]
     */
    public function findByName(string $name, string $locale): array;

    /**
     * @return ShippingMethodInterface[]
     */
    public function findEnabledWithRules(): array;
}
