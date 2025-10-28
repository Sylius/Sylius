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
    private StateBasedExtension $extension;

    /** @var MockObject&SectionProviderInterface */
    private $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new StateBasedExtension($this->sectionProvider, ['cart']);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_shop(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_does_not_apply_conditions_to_item_for_shop(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $queryBuilder->expects($this->never())->method('getRootAliases');

        $this->extension->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            [],
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_applies_conditions_to_collection_for_admin(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprNotIn = $this->createMock(Func::class);

        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);
        $queryBuilder->method('getRootAliases')->willReturn(['o']);

        $queryNameGenerator
            ->expects($this->once())
            ->method('generateParameterName')
            ->with('state')
            ->willReturn('state');

        $queryBuilder->method('expr')->willReturn($expr);
        $expr->method('notIn')->with('o.state', ':state')->willReturn($exprNotIn);

        $queryBuilder->expects($this->once())->method('andWhere')->with($exprNotIn)->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->with(
            'state',
            ['cart'],
            ArrayParameterType::STRING,
        )->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    public function test_it_applies_conditions_to_item_for_admin(): void
    {
        $adminApiSection = $this->createMock(AdminApiSection::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $expr = $this->createMock(Expr::class);
        $exprNotIn = $this->createMock(Func::class);

        $this->sectionProvider->method('getSection')->willReturn($adminApiSection);
        $queryBuilder->method('getRootAliases')->willReturn(['o']);

        $queryNameGenerator
            ->expects($this->once())
            ->method('generateParameterName')
            ->with('state')
            ->willReturn('state');

        $queryBuilder->method('expr')->willReturn($expr);
        $expr->method('notIn')->with('o.state', ':state')->willReturn($exprNotIn);

        $queryBuilder->expects($this->once())->method('andWhere')->with($exprNotIn)->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->with(
            'state',
            ['cart'],
            ArrayParameterType::STRING,
        )->willReturnSelf();

        $this->extension->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            [],
            new Get(name: Request::METHOD_GET),
        );
    }
}
