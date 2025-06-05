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

namespace Tests\Sylius\Bundle\OrderBundle\NumberAssigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssigner;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;

final class OrderNumberAssignerTest extends TestCase
{
    /** @var OrderNumberGeneratorInterface&MockObject */
    private OrderNumberGeneratorInterface $numberGenerator;

    private OrderNumberAssigner $orderNumberAssigner;

    /** @var OrderInterface&MockObject */
    private OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->numberGenerator = $this->createMock(OrderNumberGeneratorInterface::class);
        $this->orderNumberAssigner = new OrderNumberAssigner($this->numberGenerator);
        $this->order = $this->createMock(OrderInterface::class);
    }

    public function testImplementsAnOrderNumberAssignerInterface(): void
    {
        self::assertInstanceOf(OrderNumberAssignerInterface::class, $this->orderNumberAssigner);
    }

    public function testAssignsANumberToAnOrder(): void
    {
        $this->order->expects(self::once())
            ->method('getNumber')
            ->willReturn(null);

        $this->numberGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($this->order)
            ->willReturn('00000007');

        $this->order->expects(self::once())->method('setNumber')->with('00000007');

        $this->orderNumberAssigner->assignNumber($this->order);
    }

    public function testDoesNotAssignANumberToAnOrderWithNumber(): void
    {
        $this->order->expects(self::once())->method('getNumber')->willReturn('00000007');

        $this->numberGenerator->expects(self::never())->method('generate')->with($this->order);

        $this->order->expects(self::never())->method('setNumber');

        $this->orderNumberAssigner->assignNumber($this->order);
    }
}
