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
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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
    private ShopUserBasedExtension $extension;

    private MockObject|SectionProviderInterface $sectionProvider;

    private MockObject|UserContextInterface $userContext;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->extension = new ShopUserBasedExtension($this->sectionProvider, $this->userContext);
    }

    public function test_it_implements_interfaces(): void
    {
        $this->assertInstanceOf(QueryCollectionExtensionInterface::class, $this->extension);
        $this->assertInstanceOf(QueryItemExtensionInterface::class, $this->extension);
    }

    public function test_it_does_nothing_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, \stdClass::class, []);

        $this->addToAssertionCount(1);
    }

    public function test_it_does_nothing_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);
        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $this->addToAssertionCount(1);
    }

    public function test_it_does_nothing_if_user_is_not_shop_user(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));
        $this->userContext->method('getUser')->willReturn($this->createMock(AdminUserInterface::class));
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);
        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $this->addToAssertionCount(1);
    }

    public function test_it_applies_conditions_for_shop_user(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $customer = $this->createMock(CustomerInterface::class);

        $shopUser = $this->createMock(ShopUserInterface::class);
        $shopUser->method('getCustomer')->willReturn($customer);
        $this->userContext->method('getUser')->willReturn($shopUser);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $expr = $this->createMock(Expr::class);
        $comparison = $this->createMock(Expr\Comparison::class);

        $queryNameGenerator->method('generateParameterName')->with(':customer')->willReturn(':customer');
        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $queryBuilder->method('expr')->willReturn($expr);
        $expr->method('eq')->with('o.customer', ':customer')->willReturn($comparison);

        $queryBuilder->expects($this->exactly(2))->method('innerJoin')->with('o.customer', 'customer')->willReturn($queryBuilder);
        $queryBuilder->expects($this->exactly(2))->method('andWhere')->with($comparison)->willReturn($queryBuilder);
        $queryBuilder->expects($this->exactly(2))->method('setParameter')->with($this->anything(), $customer)->willReturn($queryBuilder);

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);
        $this->extension->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);
    }
}
