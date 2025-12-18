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
    private StateBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&QueryBuilder $queryBuilder;

    private MockObject&QueryNameGeneratorInterface $nameGenerator;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->nameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->extension = new StateBasedExtension(
            $this->sectionProvider,
            ['sylius_api_shop_order_get', 'sylius_api_shop_order_payment_get_configuration'],
        );
    }

    public function test_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, ResourceInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $section = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, OrderInterface::class, new Get());
    }

    public function test_applies_conditions_to_collection(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $user = $this->createMock(ShopUserInterface::class);
        $customer = $this->createMock(CustomerInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprNeq = $this->createMock(Expr\Comparison::class);

        $user->method('getCustomer')->willReturn($customer);
        $this->sectionProvider->method('getSection')->willReturn($section);

        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->nameGenerator->method('generateParameterName')->with('state')->willReturn('state');
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('neq')->with('o.state', ':state')->willReturn($exprNeq);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprNeq)->willReturnSelf();
        $this->queryBuilder->expects($this->once())->method('setParameter')->with('state', OrderInterface::STATE_CART)->willReturnSelf();

        $this->extension->applyToCollection($this->queryBuilder, $this->nameGenerator, OrderInterface::class, new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_unsupported_resource(): void
    {
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, ResourceInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_for_admin_api_section(): void
    {
        $section = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($section);
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, OrderInterface::class, [], new Get());
    }

    public function test_does_not_apply_conditions_to_item_if_operation_is_allowed(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $operation = $this->createMock(Operation::class);

        $this->sectionProvider->method('getSection')->willReturn($section);
        $operation->method('getName')->willReturn('sylius_api_shop_order_payment_get_configuration');
        $this->queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, OrderInterface::class, [], $operation);
    }

    public function test_applies_conditions_to_item(): void
    {
        $section = $this->createMock(ShopApiSection::class);
        $operation = $this->createMock(Operation::class);
        $expr = $this->createMock(Expr::class);
        $exprEq = $this->createMock(Expr\Comparison::class);

        $this->sectionProvider->method('getSection')->willReturn($section);
        $operation->method('getName')->willReturn('sylius_api_shop_order_get_custom');
        $this->queryBuilder->method('getRootAliases')->willReturn(['o']);
        $this->nameGenerator->method('generateParameterName')->with('state')->willReturn('state');
        $this->queryBuilder->method('expr')->willReturn($expr);
        $expr->method('eq')->with('o.state', ':state')->willReturn($exprEq);

        $this->queryBuilder->expects($this->once())->method('andWhere')->with($exprEq)->willReturnSelf();
        $this->queryBuilder->expects($this->once())->method('setParameter')->with('state', OrderInterface::STATE_CART)->willReturnSelf();

        $this->extension->applyToItem($this->queryBuilder, $this->nameGenerator, OrderInterface::class, [], $operation);
    }
}
