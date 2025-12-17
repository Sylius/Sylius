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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\TaxonBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class TaxonBasedExtensionTest extends TestCase
{
    private TaxonBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new TaxonBasedExtension($this->sectionProvider);
    }

    public function test_it_is_a_constraint_validator(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->extension);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);
    }

    public function test_it_does_nothing_if_filter_is_not_set(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, new Get());
    }

    public function test_it_filters_products_by_taxon(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprIn = $this->createMock(Expr\Func::class);
        $exprEq = $this->createMock(Expr\Comparison::class);
        $exprAndx = $this->createMock(Expr\Andx::class);

        $queryNameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('taxonCode')
            ->willReturn('taxonCode');

        $expectedJoinAliases = [
            'productTaxons' => 'productTaxons',
            'taxon' => 'taxon',
        ];

        $queryNameGenerator->expects($this->exactly(2))
            ->method('generateJoinAlias')
            ->willReturnCallback(function ($alias) use ($expectedJoinAliases) {
                self::assertArrayHasKey($alias, $expectedJoinAliases);

                return $expectedJoinAliases[$alias];
            });

        $queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->willReturn(['o']);

        $queryBuilder->expects($this->once())
            ->method('addSelect')
            ->with('productTaxons')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->exactly(2))
            ->method('leftJoin')
            ->willReturnCallback(function ($join, $alias, $type = null, $condition = null) use ($queryBuilder, $exprAndx) {
                if ($join === 'o.productTaxons') {
                    self::assertEquals('productTaxons', $alias);
                    self::assertEquals('WITH', $type);
                    self::assertEquals('productTaxons.product = o.id', $condition);
                } elseif ($join === 'productTaxons.taxon') {
                    self::assertEquals('taxon', $alias);
                    self::assertEquals('WITH', $type);
                    self::assertSame($exprAndx, $condition);
                } else {
                    self::fail('Unexpected leftJoin call with join: ' . $join);
                }

                return $queryBuilder;
            });

        $expr->expects($this->once())
            ->method('in')
            ->with('taxon.code', ':taxonCode')
            ->willReturn($exprIn);

        $expr->expects($this->once())
            ->method('eq')
            ->with('taxon.enabled', 'true')
            ->willReturn($exprEq);

        $expr->expects($this->once())
            ->method('andX')
            ->with($exprIn, $exprEq)
            ->willReturn($exprAndx);

        $queryBuilder->method('expr')->willReturn($expr);

        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->with('productTaxons.position', 'ASC')
            ->willReturn($queryBuilder);

        $queryBuilder->expects($this->once())
            ->method('setParameter')
            ->with('taxonCode', ['t_shirts'])
            ->willReturn($queryBuilder);

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductInterface::class,
            new Get(),
            ['filters' => ['productTaxons.taxon.code' => 't_shirts']],
        );
    }
}
