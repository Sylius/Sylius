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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRule;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;

final class PromotionRuleTest extends TestCase
{
    private MockObject&PromotionInterface $promotion;

    private PromotionRule $promotionRule;

    protected function setUp(): void
    {
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->promotionRule = new PromotionRule();
    }

    public function testShouldImplementPromotionRuleInterface(): void
    {
        $this->assertInstanceOf(PromotionRuleInterface::class, $this->promotionRule);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->promotionRule->getId());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->promotionRule->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->promotionRule->setType('T1');

        $this->assertSame('T1', $this->promotionRule->getType());
    }

    public function testShouldInitializeArrayForConfigurationByDefault(): void
    {
        $this->assertSame([], $this->promotionRule->getConfiguration());
    }

    public function testShouldConfigurationBeEmptyByDefault(): void
    {
        $this->assertEmpty($this->promotionRule->getConfiguration());
    }

    public function testShouldConfigurationBeMutable(): void
    {
        $this->promotionRule->setConfiguration(['value' => 500]);

        $this->assertSame(['value' => 500], $this->promotionRule->getConfiguration());
    }

    public function testShouldNotBeAttachedToPromotionByDefault(): void
    {
        $this->assertNull($this->promotionRule->getPromotion());
    }

    public function testShouldAttachItselfToPromotion(): void
    {
        $this->promotionRule->setPromotion($this->promotion);

        $this->assertSame($this->promotion, $this->promotionRule->getPromotion());
    }

    public function testShouldDetachItselfFromPromotion(): void
    {
        $this->promotionRule->setPromotion($this->promotion);

        $this->promotionRule->setPromotion(null);

        $this->assertNull($this->promotionRule->getPromotion());
    }
}
