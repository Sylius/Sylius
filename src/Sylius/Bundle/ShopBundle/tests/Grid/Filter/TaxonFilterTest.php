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

namespace Tests\Sylius\Bundle\ShopBundle\Grid\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource;
use Sylius\Bundle\ShopBundle\Grid\Filter\TaxonFilter;
use Sylius\Component\Grid\Data\DataSourceInterface;

#[CoversClass(TaxonFilter::class)]
final class TaxonFilterTest extends TestCase
{
    private QueryBuilder $queryBuilder;

    private TaxonFilter $filter;

    protected function setUp(): void
    {
        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('getExpressionBuilder')->willReturn(new Expr());
        $entityManager->method('createQueryBuilder')->willReturnCallback(
            static fn (): QueryBuilder => new QueryBuilder($entityManager),
        );

        $this->queryBuilder = (new QueryBuilder($entityManager))->select('o')->from('App\Entity\Product', 'o');
        $this->filter = new TaxonFilter('App\Entity\ProductTaxon', 'App\Entity\Taxon');
    }

    #[Test]
    public function it_does_nothing_when_no_taxon_is_selected(): void
    {
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'taxon', [], []);
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'taxon', ['', null], []);
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'taxon', null, []);

        $this->assertSame('SELECT o FROM App\Entity\Product o', $this->queryBuilder->getDQL());
    }

    #[Test]
    public function it_restricts_products_to_the_selected_taxons(): void
    {
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'taxon', ['t_shirts', 'caps', 't_shirts'], []);

        $this->assertSame(
            'SELECT o FROM App\Entity\Product o WHERE EXISTS(' .
            'SELECT 1 FROM App\Entity\ProductTaxon shop_taxon_filter_taxon_product_taxon ' .
            'INNER JOIN shop_taxon_filter_taxon_product_taxon.taxon shop_taxon_filter_taxon_taxon ' .
            'WHERE shop_taxon_filter_taxon_product_taxon.product = o ' .
            'AND shop_taxon_filter_taxon_taxon.code IN (:shop_taxon_filter_taxon_codes))',
            $this->queryBuilder->getDQL(),
        );
        $this->assertSame(['t_shirts', 'caps'], $this->queryBuilder->getParameter('shop_taxon_filter_taxon_codes')?->getValue());
    }

    #[Test]
    public function it_accepts_a_single_taxon_code(): void
    {
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'taxon', 'caps', []);

        $this->assertSame(['caps'], $this->queryBuilder->getParameter('shop_taxon_filter_taxon_codes')?->getValue());
    }

    #[Test]
    public function it_can_include_products_from_descendant_taxons(): void
    {
        $this->filter->apply(
            new DataSource($this->queryBuilder, true, true),
            'category',
            ['clothes'],
            ['include_all_descendants' => true],
        );

        $this->assertSame(
            'SELECT o FROM App\Entity\Product o WHERE EXISTS(' .
            'SELECT 1 FROM App\Entity\ProductTaxon shop_taxon_filter_category_product_taxon ' .
            'INNER JOIN shop_taxon_filter_category_product_taxon.taxon shop_taxon_filter_category_taxon, ' .
            'App\Entity\Taxon shop_taxon_filter_category_selected_taxon ' .
            'WHERE shop_taxon_filter_category_product_taxon.product = o ' .
            'AND shop_taxon_filter_category_selected_taxon.code IN (:shop_taxon_filter_category_codes) ' .
            'AND shop_taxon_filter_category_taxon.root = shop_taxon_filter_category_selected_taxon.root ' .
            'AND shop_taxon_filter_category_taxon.left >= shop_taxon_filter_category_selected_taxon.left ' .
            'AND shop_taxon_filter_category_taxon.right <= shop_taxon_filter_category_selected_taxon.right)',
            $this->queryBuilder->getDQL(),
        );
    }

    #[Test]
    public function it_can_be_combined_with_another_taxon_filter(): void
    {
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'collection', ['winter'], []);
        $this->filter->apply(new DataSource($this->queryBuilder, true, true), 'category', ['caps'], []);

        $this->assertStringContainsString('shop_taxon_filter_collection_codes', $this->queryBuilder->getDQL());
        $this->assertStringContainsString(')) AND (EXISTS(', $this->queryBuilder->getDQL());
        $this->assertSame(['winter'], $this->queryBuilder->getParameter('shop_taxon_filter_collection_codes')?->getValue());
        $this->assertSame(['caps'], $this->queryBuilder->getParameter('shop_taxon_filter_category_codes')?->getValue());
    }

    #[Test]
    public function it_only_supports_the_doctrine_orm_driver(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->filter->apply($this->createStub(DataSourceInterface::class), 'taxon', ['caps'], []);
    }
}
