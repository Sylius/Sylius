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
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledWithinProductAssociationExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledWithinProductAssociationExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private EnabledWithinProductAssociationExtension $enabledWithinProductAssociationExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->enabledWithinProductAssociationExtension = new EnabledWithinProductAssociationExtension($this->sectionProviderMock);
    }

    public function testAConstraintValidator(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->enabledWithinProductAssociationExtension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->enabledWithinProductAssociationExtension);
    }

    public function testDoesNothingIfCurrentResourceIsNotAProduct(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::never())->method('getSection');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->enabledWithinProductAssociationExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, TaxonInterface::class, new Get(name: Request::METHOD_GET));
    }

    public function testDoesNothingIfCurrentUserIsAnAdminUser(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->enabledWithinProductAssociationExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ProductInterface::class, new Get(name: Request::METHOD_GET));
    }

    public function testFiltersProductsByAvailableAssociations(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $comparisonMock */
        $comparisonMock = $this->createMock(Comparison::class);
        /** @var Andx|MockObject $andxMock */
        $andxMock = $this->createMock(Andx::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryNameGeneratorMock->expects($this->exactly(2))->method('generateJoinAlias')->willReturnMap([['association', 'association'], ['associatedProduct', 'associatedProduct']]);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('addSelect')->with('o')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('addSelect')->willReturnMap([['o', $queryBuilderMock], ['association', $queryBuilderMock]]);
        $exprMock->expects(self::once())->method('andX')->with($this->isInstanceOf(Comparison::class), $this->isInstanceOf(Comparison::class))->willReturn($andxMock);
        $queryBuilderMock->expects($this->exactly(2))->method('leftJoin')->willReturnMap([['o.associations', 'association', $queryBuilderMock], ['association.associatedProducts', 'associatedProduct', 'WITH', Argument::type(Andx::class), $queryBuilderMock]]);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects($this->exactly(2))->method('eq')->willReturnMap([['associatedProduct.enabled', 'true', $comparisonMock], ['association.owner', 'o', $comparisonMock]]);
        $this->enabledWithinProductAssociationExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ProductInterface::class, new Get(name: Request::METHOD_GET));
    }
}
