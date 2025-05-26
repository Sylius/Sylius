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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\PromotionActionFactory;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PromotionActionFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private MockObject&PromotionActionInterface $promotionAction;

    private PromotionActionFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->promotionAction = $this->createMock(PromotionActionInterface::class);
        $this->factory = new PromotionActionFactory($this->decoratedFactory);
    }

    public function testShouldImplementPromotionActionFactoryInterface(): void
    {
        $this->assertInstanceOf(PromotionActionFactoryInterface::class, $this->factory);
    }

    public function testShouldCreateNewActionWithDefaultActionFactory(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);

        $this->assertSame($this->promotionAction, $this->factory->createNew());
    }

    public function testShouldCreateNewFixedDiscountActionWithGivenBaseAmount(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setType')->with(FixedDiscountPromotionActionCommand::TYPE);
        $this->promotionAction->expects($this->once())->method('setConfiguration')->with(['WEB_US' => ['amount' => 1000]]);

        $this->assertSame($this->promotionAction, $this->factory->createFixedDiscount(1000, 'WEB_US'));
    }

    public function testShouldCreateUnitFixedDiscountActionWithGivenBaseAmount(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setType')->with(UnitFixedDiscountPromotionActionCommand::TYPE);
        $this->promotionAction->expects($this->once())->method('setConfiguration')->with(['WEB_US' => ['amount' => 1000]]);

        $this->assertSame($this->promotionAction, $this->factory->createUnitFixedDiscount(1000, 'WEB_US'));
    }

    public function testShouldCreatePercentageDiscountActionWithGivenDiscountRate(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setType')->with(PercentageDiscountPromotionActionCommand::TYPE);
        $this->promotionAction->expects($this->once())->method('setConfiguration')->with(['percentage' => 0.1]);

        $this->assertSame($this->promotionAction, $this->factory->createPercentageDiscount(0.1));
    }

    public function testShouldCreateUnitPercentageDiscountActionWithGivenDiscountRate(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setType')->with(UnitPercentageDiscountPromotionActionCommand::TYPE);
        $this->promotionAction->expects($this->once())->method('setConfiguration')->with(['WEB_US' => ['percentage' => 0.1]]);

        $this->assertSame($this->promotionAction, $this->factory->createUnitPercentageDiscount(0.1, 'WEB_US'));
    }

    public function testShouldCreateShippingPercentageDiscountActionWithGivenDiscountRate(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->promotionAction);
        $this->promotionAction->expects($this->once())->method('setType')->with(ShippingPercentageDiscountPromotionActionCommand::TYPE);
        $this->promotionAction->expects($this->once())->method('setConfiguration')->with(['percentage' => 0.1]);

        $this->assertSame($this->promotionAction, $this->factory->createShippingPercentageDiscount(0.1));
    }
}
