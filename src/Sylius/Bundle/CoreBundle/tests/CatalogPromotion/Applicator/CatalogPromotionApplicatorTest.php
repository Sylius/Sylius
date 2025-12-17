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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionApplicatorTest extends TestCase
{
    private ActionBasedDiscountApplicatorInterface&MockObject $actionBasedDiscountApplicator;

    private MockObject&ProductVariantForCatalogPromotionEligibilityInterface $checker;

    private CatalogPromotionEligibilityCheckerInterface&MockObject $catalogPromotionEligibilityChecker;

    private CatalogPromotionApplicator $catalogPromotionApplicator;

    protected function setUp(): void
    {
        $this->actionBasedDiscountApplicator = $this->createMock(ActionBasedDiscountApplicatorInterface::class);
        $this->checker = $this->createMock(ProductVariantForCatalogPromotionEligibilityInterface::class);
        $this->catalogPromotionEligibilityChecker = $this->createMock(CatalogPromotionEligibilityCheckerInterface::class);
        $this->catalogPromotionApplicator = new CatalogPromotionApplicator($this->actionBasedDiscountApplicator, $this->checker, $this->catalogPromotionEligibilityChecker);
    }

    public function testImplementsCatalogPromotionApplicatorInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionApplicatorInterface::class, $this->catalogPromotionApplicator);
    }

    public function testAppliesPercentageDiscountOnProductVariant(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $catalogPromotionAction = $this->createMock(CatalogPromotionActionInterface::class);
        $firstChannel = $this->createMock(ChannelInterface::class);
        $secondChannel = $this->createMock(ChannelInterface::class);
        $firstChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $secondChannelPricing = $this->createMock(ChannelPricingInterface::class);

        // Mocks behavior
        $this->checker
            ->expects($this->once())
            ->method('isApplicableOnVariant')
            ->with($catalogPromotion, $variant)
            ->willReturn(true);

        $this->catalogPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isCatalogPromotionEligible')
            ->with($catalogPromotion)
            ->willReturn(true);

        $catalogPromotion
            ->expects($this->once())
            ->method('getActions')
            ->willReturn(new ArrayCollection([$catalogPromotionAction]));

        $catalogPromotion
            ->expects($this->once())
            ->method('getChannels')
            ->willReturn(new ArrayCollection([$firstChannel, $secondChannel]));

        $catalogPromotionAction
            ->expects($this->any())
            ->method('getConfiguration')
            ->willReturn(['amount' => 0.3]);

        $variant
            ->expects($this->exactly(2))
            ->method('getChannelPricingForChannel')
            ->willReturnMap([
                [$firstChannel, $firstChannelPricing],
                [$secondChannel, $secondChannelPricing],
            ])
        ;

        $this->actionBasedDiscountApplicator
            ->expects($this->exactly(2))
            ->method('applyDiscountOnChannelPricing')
            ->with(
                $this->callback(fn ($promo) => $promo === $catalogPromotion),
                $this->callback(fn ($action) => $action === $catalogPromotionAction),
                $this->callback(
                    fn ($channelPricing) => $channelPricing === $firstChannelPricing || $channelPricing === $secondChannelPricing,
                ),
            )
        ;

        $this->catalogPromotionApplicator->applyOnVariant($variant, $catalogPromotion);
    }

    public function testDoesNothingIfPromotionIsNotApplicableOnVariants(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isCatalogPromotionEligible')
            ->with($catalogPromotion)
            ->willReturn(false)
        ;

        $this->checker->expects($this->never())->method('isApplicableOnVariant');

        $this->catalogPromotionApplicator->applyOnVariant($variant, $catalogPromotion);
    }
}
