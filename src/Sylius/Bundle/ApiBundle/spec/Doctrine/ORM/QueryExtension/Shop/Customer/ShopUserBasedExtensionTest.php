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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer\ShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Model\ResourceInterface;

final class ShopUserBasedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    /** @var UserContextInterface|MockObject */
    private MockObject $userContextMock;

    private ShopUserBasedExtension $shopUserBasedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->userContextMock = $this->createMock(UserContextInterface::class);
        $this->shopUserBasedExtension = new ShopUserBasedExtension($this->sectionProviderMock, $this->userContextMock);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ResourceInterface::class, new Get());
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
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, CustomerInterface::class, new Get());
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
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprComparisonMock */
        $exprComparisonMock = $this->createMock(Comparison::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($userMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateJoinAlias')->with('user')->willReturn('user');
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('user')->willReturn('user');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('eq')->with('user.id', ':user')->willReturn($exprComparisonMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('o.user', 'user', 'WITH', $exprComparisonMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('user', $userMock)->willReturn($queryBuilderMock);
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, CustomerInterface::class, new Get());
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, ResourceInterface::class, [], new Get());
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
        $this->userContextMock->expects(self::never())->method('getUser');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, CustomerInterface::class, [], new Get());
    }

    public function testAppliesConditionsToItem(): void
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
        /** @var Comparison|MockObject $exprComparisonMock */
        $exprComparisonMock = $this->createMock(Comparison::class);
        $userMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($userMock);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryNameGeneratorMock->expects(self::once())->method('generateJoinAlias')->with('user')->willReturn('user');
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('user')->willReturn('user');
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('eq')->with('user.id', ':user')->willReturn($exprComparisonMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('o.user', 'user', 'WITH', $exprComparisonMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('user', $userMock)->willReturn($queryBuilderMock);
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, CustomerInterface::class, [], new Get());
    }
}
