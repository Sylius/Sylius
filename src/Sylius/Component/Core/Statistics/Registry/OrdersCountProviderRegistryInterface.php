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

interface OrdersCountProviderRegistryInterface
{
    public function getByType(string $type): OrdersCountProviderInterface;
}
