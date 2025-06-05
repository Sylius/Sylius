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

namespace Tests\Sylius\Bundle\CoreBundle\Order\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;

final class OrderPromotionsIntegrityCheckerTest extends TestCase
{
    private MockObject&OrderProcessorInterface $orderProcessor;

    private OrderPromotionsIntegrityChecker $checker;

    protected function setUp(): void
    {
        $this->orderProcessor = $this->createMock(OrderProcessorInterface::class);
        $this->checker = new OrderPromotionsIntegrityChecker($this->orderProcessor);
    }

    public function testReturnsNullIfPromotionIsValid(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $promotion = $this->createMock(PromotionInterface::class);

        $order
            ->method('getPromotions')
            ->willReturnOnConsecutiveCalls(
                new ArrayCollection([$promotion]),
                new ArrayCollection([$promotion]),
            )
        ;

        $this->orderProcessor
            ->expects($this->once())
            ->method('process')
            ->with($order)
        ;

        $result = $this->checker->check($order);

        $this->assertNull($result);
    }

    public function testReturnsPromotionIfPromotionIsNotValid(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $oldPromotion = $this->createMock(PromotionInterface::class);
        $newPromotion = $this->createMock(PromotionInterface::class);

        $order
            ->method('getPromotions')
            ->willReturnOnConsecutiveCalls(
                new ArrayCollection([$oldPromotion]),
                new ArrayCollection([$newPromotion]),
            )
        ;

        $this->orderProcessor->expects($this->once())->method('process')->with($order);

        $result = $this->checker->check($order);

        $this->assertSame($oldPromotion, $result);
    }
}
