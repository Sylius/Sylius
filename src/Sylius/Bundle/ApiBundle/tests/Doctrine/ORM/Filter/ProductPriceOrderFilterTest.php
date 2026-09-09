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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductPriceOrderFilter;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;

#[AllowMockObjectsWithoutExpectations]
final class ProductPriceOrderFilterTest extends TestCase
{
    /** @var MockObject&QueryBuilder */
    private MockObject $queryBuilder;

    /** @var MockObject&QueryNameGeneratorInterface */
    private MockObject $queryNameGenerator;

    /** @var MockObject&EntityManagerInterface */
    private MockObject $entityManager;

    /** @var MockObject&ChannelInterface */
    private MockObject $channel;

    protected function setUp(): void
    {
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
    }

    #[Test]
    public function it_applies_ascending_order_by_price(): void
    {
        $this->configureQueryBuilderForValidDirection();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('channelPricing.price', 'asc')
            ->willReturnSelf()
        ;

        $this->applyFilter('asc');
    }

    #[Test]
    public function it_applies_descending_order_by_price(): void
    {
        $this->configureQueryBuilderForValidDirection();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('channelPricing.price', 'desc')
            ->willReturnSelf()
        ;

        $this->applyFilter('desc');
    }

    #[Test]
    public function it_applies_order_with_uppercase_direction(): void
    {
        $this->configureQueryBuilderForValidDirection();

        $this->queryBuilder
            ->expects($this->once())
            ->method('orderBy')
            ->with('channelPricing.price', 'asc')
            ->willReturnSelf()
        ;

        $this->applyFilter('ASC');
    }

    #[Test]
    public function it_does_not_apply_order_with_invalid_direction(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('orderBy')
        ;

        $this->applyFilter('INVALID');
    }

    #[Test]
    public function it_does_not_apply_order_with_dql_injection_payload(): void
    {
        $this->queryBuilder
            ->expects($this->never())
            ->method('orderBy')
        ;

        $this->applyFilter('ASC, variant.code DESC');
    }

    private function configureQueryBuilderForValidDirection(): void
    {
        $subQueryBuilder = $this->createMock(QueryBuilder::class);
        $subQueryBuilder->method('select')->willReturnSelf();
        $subQueryBuilder->method('innerJoin')->willReturnSelf();
        $subQueryBuilder->method('andWhere')->willReturnSelf();
        $subQueryBuilder->method('getDQL')->willReturn('SELECT min(v.position) FROM Product m INNER JOIN m.variants v WHERE m.id = :productId AND v.enabled = :enabled');

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->method('createQueryBuilder')->willReturn($subQueryBuilder);

        $this->entityManager
            ->method('getRepository')
            ->with(ProductInterface::class)
            ->willReturn($entityRepository)
        ;

        $this->queryBuilder
            ->method('getEntityManager')
            ->willReturn($this->entityManager)
        ;

        $this->queryBuilder
            ->method('getRootAliases')
            ->willReturn(['o'])
        ;

        $this->queryBuilder->method('addSelect')->willReturnSelf();
        $this->queryBuilder->method('innerJoin')->willReturnSelf();
        $this->queryBuilder->method('andWhere')->willReturnSelf();
        $this->queryBuilder->method('setParameter')->willReturnSelf();

        $this->queryBuilder
            ->method('expr')
            ->willReturn(new \Doctrine\ORM\Query\Expr())
        ;

        $this->queryNameGenerator
            ->method('generateParameterName')
            ->willReturn('param')
        ;

        $this->channel
            ->method('getCode')
            ->willReturn('WEB')
        ;
    }

    private function applyFilter(string $direction): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $filter = new ProductPriceOrderFilter($managerRegistry);
        $filter->apply(
            $this->queryBuilder,
            $this->queryNameGenerator,
            ProductInterface::class,
            null,
            [
                'filters' => ['order' => ['price' => $direction]],
                ContextKeys::CHANNEL => $this->channel,
            ],
        );
    }
}
