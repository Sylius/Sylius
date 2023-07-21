<?php

/*
 * This file is part of Sylius corporate website.
 *
 * (c) Sylius <sylius+sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Model\OrderItemInterface;

interface AdditionalFiltersApplicatorInterface
{
    /**
     * @param OrderItemInterface[] $filteredItems
     * @param array<string, mixed> $configuration
     *
     * @return OrderItemInterface[]
     */
    public function apply(array $filteredItems, array $configuration): array;
}
