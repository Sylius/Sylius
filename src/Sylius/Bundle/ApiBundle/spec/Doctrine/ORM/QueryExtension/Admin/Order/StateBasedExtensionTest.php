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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Order\StateBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;

final class StateBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private StateBasedExtension $stateBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->stateBasedExtension = new StateBasedExtension($this->sectionProviderMock, ['cart']);
    }

    public function testDoesNotApplyConditionsToCollectionForShop(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            OrderInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function testDoesNotApplyConditionsToItemForShop(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            OrderInterface::class,
            [],
            new Get(name: Request::METHOD_GET),
        );
    }

    public function testAppliesConditionsToCollectionForAdmin(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Func|MockObject $exprNotInMock */
        $exprNotInMock = $this->createMock(Func::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('state')->willReturn('state');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('notIn')->with('o.state', ':state')->willReturn($exprNotInMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprNotInMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('state', ['cart'], ArrayParameterType::STRING)->willReturn($queryBuilderMock);
        $this->stateBasedExtension->applyToCollection(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            OrderInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function testAppliesConditionsToItemForAdmin(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Func|MockObject $exprNotInMock */
        $exprNotInMock = $this->createMock(Func::class);
        $queryBuilderMock->expects($this->exactly(2))->method('getRootAliases')->willReturnMap([[['o']], [['o']]]);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('state')->willReturn('state');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('notIn')->with('o.state', ':state')->willReturn($exprNotInMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprNotInMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('state', ['cart'], ArrayParameterType::STRING)->willReturn($queryBuilderMock);
        $this->stateBasedExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            OrderInterface::class,
            [],
            new Get(name: Request::METHOD_GET),
        );
    }
}
