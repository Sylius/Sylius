<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Promotion\Filter;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(array $items, array $configuration)
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
