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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\StateBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Model\ResourceInterface;

final class StateBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private StateBasedExtension $stateBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->stateBasedExtension = new StateBasedExtension($this->sectionProviderMock, ['sylius_api_shop_order_get', 'sylius_api_shop_order_payment_get_configuration']);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ResourceInterface::class, new Get());
    }

    public function testDoesNotApplyConditionsToCollectionForAdminApiSection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var AdminApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, new Get());
    }

    public function testAppliesConditionsToCollection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        /** @var ShopUserInterface|MockObject $userMock */
        $userMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprNeqMock */
        $exprNeqMock = $this->createMock(Comparison::class);
        $userMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('state')->willReturn('state');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('neq')->with('o.state', ':state')->willReturn($exprNeqMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprNeqMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('state', OrderInterface::STATE_CART)->willReturn($queryBuilderMock);
        $this->stateBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, new Get());
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ResourceInterface::class, [], new Get());
    }

    public function testDoesNotApplyConditionsToItemForAdminApiSection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var AdminApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], new Get());
    }

    public function testDoesNotApplyConditionsToItemIfOperationIsAllowed(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $operationMock->expects(self::once())->method('getName')->willReturn('sylius_api_shop_order_payment_get_configuration');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->stateBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], $operationMock);
    }

    public function testAppliesConditionsToItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var ShopApiSection|MockObject $sectionMock */
        $sectionMock = $this->createMock(ShopApiSection::class);
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprEqMock */
        $exprEqMock = $this->createMock(Comparison::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $operationMock->expects(self::once())->method('getName')->willReturn('sylius_api_shop_order_get_custom');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('state')->willReturn('state');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('eq')->with('o.state', ':state')->willReturn($exprEqMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprEqMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('state', OrderInterface::STATE_CART)->willReturn($queryBuilderMock);
        $this->stateBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, OrderInterface::class, [], $operationMock);
    }
}
