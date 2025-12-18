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
use Sylius\Component\Promotion\Model\PromotionAction;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class PromotionActionTest extends TestCase
{
    private MockObject&PromotionInterface $promotion;

    private PromotionAction $promotionAction;

    protected function setUp(): void
    {
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->promotionAction = new PromotionAction();
    }

    public function testShouldImplementPromotionActionInterface(): void
    {
        $this->assertInstanceOf(PromotionActionInterface::class, $this->promotionAction);
    }

    public function testShouldNotHaveIdByDefault(): void
    {
        $this->assertNull($this->promotionAction->getId());
    }

    public function testShouldNotHaveTypeByDefault(): void
    {
        $this->assertNull($this->promotionAction->getType());
    }

    public function testShouldTypeBeMutable(): void
    {
        $this->promotionAction->setType('test_action');

        $this->assertSame('test_action', $this->promotionAction->getType());
    }

    public function testShouldInitializeArrayForConfigurationByDefault(): void
    {
        $this->assertSame([], $this->promotionAction->getConfiguration());
    }

    public function testShouldConfigurationBeEmptyByDefault(): void
    {
        $this->assertEmpty($this->promotionAction->getConfiguration());
    }

    public function testShouldConfigurationBeMutable(): void
    {
        $this->promotionAction->setConfiguration(['value' => 500]);

        $this->assertSame(['value' => 500], $this->promotionAction->getConfiguration());
    }

    public function testShouldNotBeAttachedToPromotionByDefault(): void
    {
        $this->assertNull($this->promotionAction->getPromotion());
    }

    public function testShouldAttachItselfToPromotion(): void
    {
        $this->promotionAction->setPromotion($this->promotion);

        $this->assertSame($this->promotion, $this->promotionAction->getPromotion());
    }

    public function testShouldDetachItselfFromPromotion(): void
    {
        $this->promotionAction->setPromotion($this->promotion);

        $this->promotionAction->setPromotion(null);

        $this->assertNull($this->promotionAction->getPromotion());
    }

    public function testShouldClearConfigurationWhenTypeChange(): void
    {
        $this->promotionAction->setType('type_one');
        $this->promotionAction->setConfiguration(['foo' => 'bar']);

        $this->promotionAction->setType('type_two');

        $this->assertSame([], $this->promotionAction->getConfiguration());
    }

    public function testShoulNotClearConfigurationWhenTypeIsSetTwiceToTheSameValue(): void
    {
        $this->promotionAction->setType('type_one');
        $this->promotionAction->setConfiguration(['foo' => 'bar']);

        $this->promotionAction->setType('type_one');

        $this->assertSame(['foo' => 'bar'], $this->promotionAction->getConfiguration());
    }
}
