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

namespace Tests\Sylius\Bundle\ShopBundle\Grid\Mutator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Grid\Filter\TaxonFilter;
use Sylius\Bundle\ShopBundle\Grid\Mutator\LiveProductGridMutator;
use Sylius\Bundle\ShopBundle\Grid\ProductGrid;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\GridComponent;
use Sylius\Component\Grid\Builder\GridBuilder;

#[CoversClass(LiveProductGridMutator::class)]
final class LiveProductGridMutatorTest extends TestCase
{
    #[Test]
    public function it_adapts_the_product_grid_to_the_live_component(): void
    {
        $gridBuilder = GridBuilder::create('sylius_shop_product', 'App\Entity\Product');
        (new ProductGrid())($gridBuilder);

        (new LiveProductGridMutator())($gridBuilder);

        $grid = $gridBuilder->toArray();

        $this->assertSame([
            'method' => 'createShopListQueryBuilderForChannel',
            'arguments' => [],
        ], $grid['driver']['options']['repository']);

        $this->assertSame(['search', 'taxon'], array_keys($grid['filters']));
        $this->assertSame('sylius.ui.search_products', $grid['filters']['search']['label']);
        $this->assertSame('@SyliusShop/grid/filter/live/string.html.twig', $grid['filters']['search']['template']);
        $this->assertSame(TaxonFilter::TYPE, $grid['filters']['taxon']['type']);
        $this->assertTrue($grid['filters']['taxon']['options'][GridComponent::HIDDEN_WITHIN_TAXON_OPTION]);

        // Fields and limits of the regular product grid are kept
        $this->assertSame(['createdAt', 'position', 'name', 'price'], array_keys($grid['fields']));
        $this->assertSame([9, 18, 27], $grid['limits']);
    }
}
