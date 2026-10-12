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

namespace Sylius\Bundle\ShopBundle\Grid\Mutator;

use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\ShopBundle\Grid\Filter\ProductTaxonFilter;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\GridComponent;
use Sylius\Component\Grid\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;

/**
 * Adapts the "sylius_shop_product" grid to the "sylius_shop:product:grid" live component.
 * Only registered when the "sylius_shop.product_grid.live" option is enabled.
 *
 * The repository arguments (channel, locale, sorting, taxon...) are passed by the live component itself,
 * the taxon being given by the controller through the template.
 *
 * @experimental
 */
final class LiveProductGridMutator implements GridMutatorInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->setRepositoryMethod('createShopListQueryBuilderForChannel')
            ->withFilters(
                Filter::create('search', 'shop_string')
                    ->setLabel('sylius.ui.search_products')
                    ->setTemplate('@SyliusShop/product/live_index/content/grid/filter/string.html.twig')
                    ->addOption('fields', ['translation.name'])
                    ->addFormOption('type', 'contains'),
                Filter::create('taxon', ProductTaxonFilter::TYPE)
                    ->setLabel('sylius.ui.category')
                    ->setTemplate('@SyliusShop/product/live_index/content/grid/filter/taxon.html.twig')
                    ->addOption('include_all_descendants', true)
                    // Within a taxon page, the products are already restricted to this taxon
                    ->addOption(GridComponent::HIDDEN_WITHIN_TAXON_OPTION, true),
            )
        ;
    }
}
