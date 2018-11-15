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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(array $items, array $configuration): array
    {
        if (empty($configuration['filters']['taxons_filter']['taxons'])) {
            return $items;
        }

        $filteredItems = [];
        foreach ($items as $item) {
            if ($this->hasProductValidTaxon($item->getProduct(), $configuration['filters']['taxons_filter']['taxons'])) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    /**
     * @param TaxonInterface[] $taxons
     */
    private function hasProductValidTaxon(ProductInterface $product, array $taxons): bool
    {
        foreach ($product->getTaxons() as $taxon) {
            if (in_array($taxon->getCode(), $taxons, true)) {
                return true;
            }
        }

        return false;
    }
}
