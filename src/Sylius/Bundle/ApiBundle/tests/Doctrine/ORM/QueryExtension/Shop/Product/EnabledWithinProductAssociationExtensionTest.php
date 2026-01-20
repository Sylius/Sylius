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
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledWithinProductAssociationExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledWithinProductAssociationExtensionTest extends TestCase
{
    private EnabledWithinProductAssociationExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $queryNameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension = new EnabledWithinProductAssociationExtension($this->sectionProvider);
    }

    public function test_it_is_a_query_extension(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->extension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->extension);
    }

    public function test_it_does_nothing_if_resource_is_not_product(): void
    {
        $this->sectionProvider->expects(self::never())->method('getSection');
        $this->queryBuilder->expects(self::never())->method('getRootAliases');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            TaxonInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_does_nothing_for_admin_user(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());
        $this->queryBuilder->expects(self::never())->method('getRootAliases');

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_filters_products_by_available_associations(): void
    {
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());

        $this->queryNameGenerator
            ->expects(self::exactly(2))
            ->method('generateJoinAlias')
            ->willReturnCallback(static function (string $field) {
                return $field;
            });

        $this->queryBuilder
            ->method('getRootAliases')
            ->willReturn(['o']);

        $this->queryBuilder->expects(self::exactly(2))
            ->method('addSelect')
            ->willReturnCallback(function ($select) {
                static $index = 0;
                $expected = ['o', 'association'][$index++];
                self::assertEquals($expected, $select);

                return $this->queryBuilder;
            });

        $expr = $this->createMock(Expr::class);
        $comparison1 = $this->createMock(Comparison::class);
        $comparison2 = $this->createMock(Comparison::class);
        $andx = $this->createMock(Andx::class);

        $expr->expects(self::exactly(2))
            ->method('eq')
            ->willReturnMap([
                ['associatedProduct.enabled', 'true', $comparison1],
                ['association.owner', 'o', $comparison2],
            ]);

        $expr->expects(self::once())
            ->method('andX')
            ->with($comparison1, $comparison2)
            ->willReturn($andx);

        $this->queryBuilder
            ->method('expr')
            ->willReturn($expr);

        $expectedLeftJoins = [
            ['o.associations', 'association', null, null],
            ['association.associatedProducts', 'associatedProduct', 'WITH', $andx],
        ];
        $joinIndex = 0;

        $this->queryBuilder->expects(self::exactly(2))
            ->method('leftJoin')
            ->willReturnCallback(function ($join, $alias, $condType = null, $cond = null) use (&$joinIndex, $expectedLeftJoins) {
                self::assertEquals($expectedLeftJoins[$joinIndex][0], $join);
                self::assertEquals($expectedLeftJoins[$joinIndex][1], $alias);
                self::assertEquals($expectedLeftJoins[$joinIndex][2], $condType);
                self::assertEquals($expectedLeftJoins[$joinIndex][3], $cond);
                ++$joinIndex;

                return $this->queryBuilder;
            });

        $this->queryBuilder
            ->expects(self::once())
            ->method('andWhere')
            ->with('o.associations IS EMPTY OR associatedProduct.id IS NOT NULL')
            ->willReturnSelf();

        $this->extension->applyToCollection(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }
}
