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

namespace Tests\Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

final class PromotionTest extends TestCase
{
    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&PromotionRuleInterface $promotionRule;

    private MockObject&PromotionActionInterface $promotionAction;

    private Promotion $promotion;

    protected function setUp(): void
    {
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->promotionRule = $this->createMock(PromotionRuleInterface::class);
        $this->promotionAction = $this->createMock(PromotionActionInterface::class);
        $this->promotion = new Promotion();
        $this->promotion->setCurrentLocale('en_US');
        $this->promotion->setFallbackLocale('en_US');
    }

    public function testShouldBePromotion(): void
    {
        $this->assertInstanceOf(Promotion::class, $this->promotion);
    }

    public function testShouldImplementPromotionInterface(): void
    {
        $this->assertInstanceOf(PromotionInterface::class, $this->promotion);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->promotion->getId());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->promotion->setCode('P1');

        $this->assertSame('P1', $this->promotion->getCode());
    }

    public function testShouldNameBeMutable(): void
    {
        $this->promotion->setName('New Year Sale');

        $this->assertSame('New Year Sale', $this->promotion->getName());
    }

    public function testShouldDescriptionBeMutable(): void
    {
        $this->promotion->setDescription('New Year Sale 50% off.');

        $this->assertSame('New Year Sale 50% off.', $this->promotion->getDescription());
    }

    public function testShouldPriorityBeMutable(): void
    {
        $this->promotion->setPriority(5);

        $this->assertSame(5, $this->promotion->getPriority());
    }

    public function testShouldBeNotExclusiveByDefault(): void
    {
        $this->assertFalse($this->promotion->isExclusive());
    }

    public function testShouldExclusiveBeMutable(): void
    {
        $this->promotion->setExclusive(true);

        $this->assertTrue($this->promotion->isExclusive());
    }

    public function testShouldNotHaveUsageLimitByDefault(): void
    {
        $this->assertNull($this->promotion->getUsageLimit());
    }

    public function testShouldUsageLimitBeMutable(): void
    {
        $this->promotion->setUsageLimit(10);

        $this->assertSame(10, $this->promotion->getUsageLimit());
    }

    public function testShouldNotHaveUsedByDefault(): void
    {
        $this->assertSame(0, $this->promotion->getUsed());
    }

    public function testShouldUsedBeMutable(): void
    {
        $this->promotion->setUsed(5);

        $this->assertSame(5, $this->promotion->getUsed());
    }

    public function testShouldIncrementUsedValue(): void
    {
        $this->promotion->incrementUsed();

        $this->assertSame(1, $this->promotion->getUsed());
    }

    public function testShouldDecrementUsedValue(): void
    {
        $this->promotion->setUsed(5);

        $this->promotion->decrementUsed();

        $this->assertSame(4, $this->promotion->getUsed());
    }

    public function testShouldStartsAtDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->promotion->setStartsAt($date);

        $this->assertSame($date, $this->promotion->getStartsAt());
    }

    public function testShouldEndsAtDateBeMutable(): void
    {
        $date = new \DateTime();

        $this->promotion->setEndsAt($date);

        $this->assertSame($date, $this->promotion->getEndsAt());
    }

    public function testShouldInitializeCouponsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->promotion->getCoupons());
    }

    public function testShouldNotContainAnyCouponsByDefault(): void
    {
        $this->assertFalse($this->promotion->hasCoupons());
    }

    public function testShouldAddCouponsProperly(): void
    {
        $this->promotionCoupon->expects($this->once())->method('setPromotion')->with($this->promotion);

        $this->promotion->addCoupon($this->promotionCoupon);

        $this->assertTrue($this->promotion->hasCoupon($this->promotionCoupon));
    }

    public function testShouldRemoveCouponsProperly(): void
    {
        $this->promotion->addCoupon($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('setPromotion')->with(null);

        $this->promotion->removeCoupon($this->promotionCoupon);

        $this->assertFalse($this->promotion->hasCoupon($this->promotionCoupon));
    }

    public function testShouldInitializeRulesCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->promotion->getRules());
    }

    public function testShouldNotContainAnyRulesByDefault(): void
    {
        $this->assertFalse($this->promotion->hasRules());
    }

    public function testShouldAddRulesProperly(): void
    {
        $this->promotionRule->expects($this->once())->method('setPromotion')->with($this->promotion);

        $this->promotion->addRule($this->promotionRule);

        $this->assertTrue($this->promotion->hasRule($this->promotionRule));
    }

    public function testShouldRemoveRulesProperly(): void
    {
        $this->promotion->addRule($this->promotionRule);
        $this->promotionRule->expects($this->once())->method('setPromotion')->with(null);

        $this->promotion->removeRule($this->promotionRule);

        $this->assertFalse($this->promotion->hasRule($this->promotionRule));
    }

    public function testShouldInitializeActionsCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->promotion->getActions());
    }

    public function testShouldNotContainAnyActionsByDefault(): void
    {
        $this->assertFalse($this->promotion->hasActions());
    }

    public function testShouldAddActionsProperly(): void
    {
        $this->promotionAction->expects($this->once())->method('setPromotion')->with($this->promotion);

        $this->promotion->addAction($this->promotionAction);

        $this->assertTrue($this->promotion->hasAction($this->promotionAction));
    }

    public function testShouldRemoveActionsProperly(): void
    {
        $this->promotion->addAction($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setPromotion')->with(null);

        $this->promotion->removeAction($this->promotionAction);

        $this->assertFalse($this->promotion->hasAction($this->promotionAction));
    }
}
