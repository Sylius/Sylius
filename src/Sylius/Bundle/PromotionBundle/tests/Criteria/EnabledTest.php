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
use Sylius\Bundle\PromotionBundle\Criteria\Enabled;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class EnabledTest extends TestCase
{
    private Enabled $enabled;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enabled = new Enabled();
    }

    public function testImplementsCriteriaInterface(): void
    {
        self::assertInstanceOf(CriteriaInterface::class, $this->enabled);
    }

    public function testAddsFiltersToQueryBuilder(): void
    {
        /** @var QueryBuilder&MockObject $queryBuilder */
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $queryBuilder->expects(self::once())->method('getRootAliases')->willReturn(['catalog_promotion']);

        $queryBuilder->expects(self::once())
            ->method('andWhere')
            ->with('catalog_promotion.enabled = :enabled')
            ->willReturn($queryBuilder);

        $queryBuilder->expects(self::once())
            ->method('setParameter')
            ->with('enabled', true)
            ->willReturn($queryBuilder);

        self::assertSame($queryBuilder, $this->enabled->filterQueryBuilder($queryBuilder));
    }

    public function testVerifiesCatalogPromotion(): void
    {
        /** @var CatalogPromotionInterface&MockObject $catalogPromotion */
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $catalogPromotion->method('isEnabled')->willReturnOnConsecutiveCalls(true, false);

        self::assertTrue($this->enabled->verify($catalogPromotion));
        self::assertFalse($this->enabled->verify($catalogPromotion));
    }
}
