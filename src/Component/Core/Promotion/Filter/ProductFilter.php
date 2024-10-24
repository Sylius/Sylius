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

namespace Sylius\Component\Core\Promotion\Filter;

final class ProductFilter implements FilterInterface
{
    public function filter(array $items, array $configuration): array
    {
        if (empty($configuration['filters']['products_filter']['products'])) {
            return $items;
        }

        $filteredItems = [];
        foreach ($items as $item) {
            if (in_array($item->getProduct()->getCode(), $configuration['filters']['products_filter']['products'], true)) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }
}
