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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Address;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Address\ShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

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

    public function testInitializable(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->shopUserBasedExtension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->shopUserBasedExtension);
    }

    public function testDoesNotApplyConditionsToCollectionForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToItemForUnsupportedResource(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, stdClass::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToCollectionForAdminApiSection(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToItemForAdminApiSection(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToCollectionIfUserIsNotShopUser(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($adminUserMock);
        $this->shopUserBasedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testDoesNotApplyConditionsToItemIfUserIsNotShopUser(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var AdminUserInterface|MockObject $adminUserMock */
        $adminUserMock = $this->createMock(AdminUserInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($adminUserMock);
        $this->shopUserBasedExtension->applyToItem($queryBuilderMock, $queryNameGeneratorMock, AddressInterface::class, []);
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->shouldNotHaveBeenCalled();
        $queryBuilderMock->expects(self::once())->method('andWhere')->shouldNotHaveBeenCalled();
    }

    public function testAppliesConditionsToCollectionForShopApiSection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprComparisonMock */
        $exprComparisonMock = $this->createMock(Comparison::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with(':customer')->willReturn(':customer');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('eq')->with('o.customer', ':customer')->willReturn($exprComparisonMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('o.customer', 'customer')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprComparisonMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([[':customer', $customerMock, $queryBuilderMock], [':customer', $customerMock]]);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with(':customer', $customerMock)->shouldHaveBeenCalledOnce();
    }

    public function testAppliesConditionsToItemForShopApiSection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        /** @var ShopUserInterface|MockObject $shopUserMock */
        $shopUserMock = $this->createMock(ShopUserInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var Expr|MockObject $exprMock */
        $exprMock = $this->createMock(Expr::class);
        /** @var Comparison|MockObject $exprComparisonMock */
        $exprComparisonMock = $this->createMock(Comparison::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        $this->userContextMock->expects(self::once())->method('getUser')->willReturn($shopUserMock);
        $shopUserMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with(':customer')->willReturn(':customer');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('expr')->willReturn($exprMock);
        $exprMock->expects(self::once())->method('eq')->with('o.customer', ':customer')->willReturn($exprComparisonMock);
        $queryBuilderMock->expects(self::once())->method('innerJoin')->with('o.customer', 'customer')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with($exprComparisonMock)->willReturn($queryBuilderMock);
        $queryBuilderMock->expects($this->exactly(2))->method('setParameter')->willReturnMap([[':customer', $customerMock, $queryBuilderMock], [':customer', $customerMock]]);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with(':customer', $customerMock)->shouldHaveBeenCalledOnce();
    }
}
