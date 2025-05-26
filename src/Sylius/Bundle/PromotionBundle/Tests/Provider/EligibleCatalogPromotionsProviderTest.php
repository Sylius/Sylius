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

namespace Tests\Sylius\Bundle\PromotionBundle\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProvider;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class EligibleCatalogPromotionsProviderTest extends TestCase
{
    private CatalogPromotionRepositoryInterface&MockObject $catalogPromotionRepository;

    private CriteriaInterface&MockObject $firstCriterion;

    private CriteriaInterface&MockObject $secondCriterion;

    private EligibleCatalogPromotionsProvider $eligibleCatalogPromotionsProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catalogPromotionRepository = $this->createMock(CatalogPromotionRepositoryInterface::class);
        $this->firstCriterion = $this->createMock(CriteriaInterface::class);
        $this->secondCriterion = $this->createMock(CriteriaInterface::class);
        $this->eligibleCatalogPromotionsProvider = new EligibleCatalogPromotionsProvider(
            $this->catalogPromotionRepository,
            [$this->firstCriterion, $this->secondCriterion],
        );
    }

    public function testImplementsEligibleCatalogPromotionsProviderInterface(): void
    {
        self::assertInstanceOf(
            EligibleCatalogPromotionsProviderInterface::class,
            $this->eligibleCatalogPromotionsProvider,
        );
    }

    public function testProvidesCatalogPromotionsBasedOnCriteria(): void
    {
        /** @var CatalogPromotionInterface&MockObject $catalogPromotion */
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        /** @var CatalogPromotionInterface&MockObject $secondCatalogPromotion */
        $secondCatalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository->expects(self::once())
            ->method('findByCriteria')
            ->with([$this->firstCriterion, $this->secondCriterion])
            ->willReturn([$catalogPromotion, $secondCatalogPromotion]);

        self::assertSame(
            [$catalogPromotion, $secondCatalogPromotion],
            $this->eligibleCatalogPromotionsProvider->provide(),
        );
    }
}
