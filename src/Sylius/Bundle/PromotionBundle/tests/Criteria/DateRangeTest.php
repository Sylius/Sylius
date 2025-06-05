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

namespace Tests\Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Criteria\DateRange;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Symfony\Component\Clock\ClockInterface;

final class DateRangeTest extends TestCase
{
    private ClockInterface&MockObject $clock;

    private DateRange $dateRange;

    private CatalogPromotionInterface&MockObject $catalogPromotion;

    private \DateTimeImmutable $tomorrow;

    private \DateTimeImmutable $yesterday;

    private \DateTimeImmutable $now;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clock = $this->createMock(ClockInterface::class);
        $this->dateRange = new DateRange($this->clock);
        $this->catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->tomorrow = new \DateTimeImmutable('+1day');
        $this->yesterday = new \DateTimeImmutable('-1day');
        $this->now = new \DateTimeImmutable();
    }

    public function testImplementsCriteriaInterface(): void
    {
        self::assertInstanceOf(CriteriaInterface::class, $this->dateRange);
    }

    public function testAddsFiltersToQueryBuilder(): void
    {
        /** @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $queryBuilder->expects(self::once())->method('getRootAliases')->willReturn(['o']);

        $this->setupClockExpectation();

        $queryBuilder->expects(self::exactly(2))
            ->method('andWhere')
            ->willReturnMap(
                [
                    [
                        'o.startDate IS NULL OR o.startDate <= :date',
                        $queryBuilder,
                    ],
                    [
                        'o.endDate IS NULL OR o.endDate > :date',
                        $queryBuilder,
                    ],
                ],
            );

        $queryBuilder->expects(self::once())
            ->method('setParameter')
            ->with('date', $this->now)
            ->willReturn($queryBuilder);

        self::assertSame($queryBuilder, $this->dateRange->filterQueryBuilder($queryBuilder));
    }

    public function testVerifiesCatalogPromotionWithValidDateRange(): void
    {
        $this->setupClockExpectation();

        $this->catalogPromotion->method('getStartDate')->willReturn($this->yesterday);
        $this->catalogPromotion->method('getEndDate')->willReturn($this->tomorrow);

        self::assertTrue($this->dateRange->verify($this->catalogPromotion));
    }

    public function testVerifiesCatalogPromotionWithNullDates(): void
    {
        $this->setupClockExpectation();

        $this->catalogPromotion->method('getStartDate')->willReturn(null);
        $this->catalogPromotion->method('getEndDate')->willReturn(null);

        self::assertTrue($this->dateRange->verify($this->catalogPromotion));
    }

    public function testVerifiesCatalogPromotionWithInvalidDateRange(): void
    {
        $this->setupClockExpectation();

        $this->catalogPromotion->method('getStartDate')->willReturn($this->tomorrow);
        $this->catalogPromotion->method('getEndDate')->willReturn($this->yesterday);

        self::assertFalse($this->dateRange->verify($this->catalogPromotion));
    }

    private function setupClockExpectation(): void
    {
        $this->clock->expects(self::once())
            ->method('now')
            ->willReturn($this->now);
    }
}
