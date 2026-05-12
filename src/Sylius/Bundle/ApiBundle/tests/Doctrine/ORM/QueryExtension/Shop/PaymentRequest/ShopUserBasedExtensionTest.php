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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentRequest;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentRequest\ShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Resource\Model\ResourceInterface;

final class ShopUserBasedExtensionTest extends TestCase
{
    private ShopUserBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->extension = new ShopUserBasedExtension($this->sectionProvider, $this->userContext);
    }

    public function test_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $nameGenerator, ResourceInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $section = $this->createMock(AdminApiSection::class);

        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $nameGenerator, PaymentRequestInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_collection_for_anonymous_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn(null);
        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $nameGenerator, PaymentRequestInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_collection_for_non_shop_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $user = $this->createMock(AdminUserInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn($user);
        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $nameGenerator, PaymentRequestInterface::class, new Get());
    }

    public function test_applies_conditions_to_collection(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprEq = $this->createMock(Comparison::class);

        $user->method('getCustomer')->willReturn($customer);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn($user);

        $queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);

        $nameGenerator->expects($this->exactly(2))
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $alias) => $alias);
        $nameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('customer')
            ->willReturn('customer');

        $queryBuilder->expects($this->exactly(2))
            ->method('innerJoin')
            ->willReturnCallback(function (string $join, string $alias) use ($queryBuilder): QueryBuilder {
                static $calls = 0;
                ++$calls;
                if (1 === $calls) {
                    self::assertSame('o.payment', $join);
                    self::assertSame('payment', $alias);
                } else {
                    self::assertSame('payment.order', $join);
                    self::assertSame('order', $alias);
                }

                return $queryBuilder;
            });

        $queryBuilder->expects($this->once())->method('expr')->willReturn($expr);
        $expr->expects($this->once())->method('eq')->with('order.customer', ':customer')->willReturn($exprEq);

        $queryBuilder->expects($this->once())->method('andWhere')->with($exprEq)->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('setParameter')->with('customer', $customer)->willReturn($queryBuilder);

        $this->extension->applyToCollection($queryBuilder, $nameGenerator, PaymentRequestInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, ResourceInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $section = $this->createMock(AdminApiSection::class);

        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_anonymous_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn(null);
        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_non_shop_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $user = $this->createMock(AdminUserInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn($user);
        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }

    public function test_applies_conditions_to_item(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprEq = $this->createMock(Comparison::class);

        $user->method('getCustomer')->willReturn($customer);
        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn($user);

        $queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);

        $nameGenerator->expects($this->exactly(2))
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $alias) => $alias);
        $nameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('customer')
            ->willReturn('customer');

        $queryBuilder->expects($this->exactly(2))
            ->method('innerJoin')
            ->willReturnCallback(function (string $join, string $alias) use ($queryBuilder): QueryBuilder {
                static $calls = 0;
                ++$calls;
                if (1 === $calls) {
                    self::assertSame('o.payment', $join);
                    self::assertSame('payment', $alias);
                } else {
                    self::assertSame('payment.order', $join);
                    self::assertSame('order', $alias);
                }

                return $queryBuilder;
            });

        $queryBuilder->expects($this->once())->method('expr')->willReturn($expr);
        $expr->expects($this->once())->method('eq')->with('order.customer', ':customer')->willReturn($exprEq);

        $queryBuilder->expects($this->once())->method('andWhere')->with($exprEq)->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('setParameter')->with('customer', $customer)->willReturn($queryBuilder);

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }
}
