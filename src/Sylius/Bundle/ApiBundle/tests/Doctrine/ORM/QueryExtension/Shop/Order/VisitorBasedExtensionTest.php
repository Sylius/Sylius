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
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\VisitorBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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

    public function test_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, ResourceInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $section = $this->createMock(AdminApiSection::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $this->userContext->expects($this->never())->method('getUser');
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, OrderInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_if_user_is_not_null(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $section = $this->createMock(ShopApiSection::class);
        $user = $this->createMock(ShopUserInterface::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $this->userContext->expects($this->once())->method('getUser')->willReturn($user);
        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($queryBuilder, $nameGenerator, OrderInterface::class, [], new Get());
    }

    public function test_applies_conditions_to_item(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $section = $this->createMock(ShopApiSection::class);
        $expr = $this->createMock(Expr::class);
        $exprEq = $this->createMock(Comparison::class);
        $exprAndx = $this->createMock(Andx::class);
        $exprOrx = $this->createMock(Orx::class);

        $this->sectionProvider->expects($this->once())->method('getSection')->willReturn($section);
        $this->userContext->expects($this->once())->method('getUser')->willReturn(null);

        $queryBuilder->expects($this->once())->method('getRootAliases')->willReturn(['o']);

        $nameGenerator->expects($this->exactly(2))
            ->method('generateJoinAlias')
            ->with($this->logicalOr('customer', 'user'))
            ->willReturnCallback(function ($arg) {
                return $arg;
            });
        $nameGenerator->expects($this->once())
            ->method('generateParameterName')
            ->with('createdByGuest')
            ->willReturn('createdByGuest');

        $queryBuilder->expects($this->exactly(2))
            ->method('leftJoin')
            ->with($this->logicalOr('o.customer', 'customer.user'), $this->logicalOr('customer', 'user'))
            ->willReturn($queryBuilder);

        $queryBuilder->method('expr')->willReturn($expr);

        $nullResults = [
            'user' => 'user IS NULL',
            'o.customer' => 'o.customer IS NULL',
        ];

        $expr->expects($this->exactly(2))
            ->method('isNull')
            ->willReturnCallback(function ($field) use ($nullResults) {
                self::assertArrayHasKey($field, $nullResults);

                return $nullResults[$field];
            });

        $expr->expects($this->once())->method('isNotNull')->with('user')->willReturn('user IS NOT NULL');
        $expr->expects($this->once())->method('eq')->with('o.createdByGuest', ':createdByGuest')->willReturn($exprEq);
        $expr->expects($this->once())->method('andX')->with('user IS NOT NULL', $exprEq)->willReturn($exprAndx);
        $expr->expects($this->once())->method('orX')->with('user IS NULL', 'o.customer IS NULL', $exprAndx)->willReturn($exprOrx);

        $queryBuilder->expects($this->once())->method('andWhere')->with($exprOrx)->willReturn($queryBuilder);
        $queryBuilder->expects($this->once())->method('setParameter')->with('createdByGuest', true)->willReturn($queryBuilder);

        $this->extension->applyToItem($queryBuilder, $nameGenerator, OrderInterface::class, [], new Get());
    }
}
