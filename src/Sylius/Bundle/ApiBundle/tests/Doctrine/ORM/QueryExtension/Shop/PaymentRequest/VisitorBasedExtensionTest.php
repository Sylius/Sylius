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
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentRequest\VisitorBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Resource\Model\ResourceInterface;

final class VisitorBasedExtensionTest extends TestCase
{
    private VisitorBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&UserContextInterface $userContext;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->userContext = $this->createMock(UserContextInterface::class);
        $this->extension = new VisitorBasedExtension($this->sectionProvider, $this->userContext);
    }

    public function test_does_not_apply_conditions_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, ResourceInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_for_admin_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new AdminApiSection());
        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_for_authenticated_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn($this->createMock(ShopUserInterface::class));
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }

    public function test_applies_guest_order_filter_for_anonymous_user(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $expr = $this->createMock(Expr::class);
        $orX = $this->createMock(Orx::class);

        $this->sectionProvider->method('getSection')->willReturn(new ShopApiSection());
        $this->userContext->method('getUser')->willReturn(null);

        $queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);

        $nameGenerator->expects($this->exactly(4))
            ->method('generateJoinAlias')
            ->willReturnCallback(fn (string $alias) => $alias);

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

        $queryBuilder->expects($this->exactly(2))
            ->method('leftJoin')
            ->willReturnCallback(function (string $join, string $alias) use ($queryBuilder): QueryBuilder {
                static $calls = 0;
                ++$calls;
                if (1 === $calls) {
                    self::assertSame('order.customer', $join);
                    self::assertSame('customer', $alias);
                } else {
                    self::assertSame('customer.user', $join);
                    self::assertSame('user', $alias);
                }

                return $queryBuilder;
            });

        $queryBuilder->expects($this->exactly(3))->method('expr')->willReturn($expr);
        $expr->expects($this->exactly(2))
            ->method('isNull')
            ->willReturnCallback(fn (string $alias) => $alias . ' IS NULL');
        $expr->expects($this->once())->method('orX')->with('customer IS NULL', 'user IS NULL')->willReturn($orX);

        $queryBuilder->expects($this->once())->method('andWhere')->with($orX)->willReturn($queryBuilder);

        $this->extension->applyToItem($queryBuilder, $nameGenerator, PaymentRequestInterface::class, [], new Get());
    }
}
