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

namespace Tests\Sylius\Component\Order\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Order\Model\OrderSequence;
use Sylius\Component\Order\Model\OrderSequenceInterface;

final class OrderSequenceTest extends TestCase
{
    private OrderSequence $orderSequence;

    protected function setUp(): void
    {
        $this->orderSequence = new OrderSequence();
    }

    public function testImplementsOrderSequenceInterface(): void
    {
        $this->assertInstanceOf(OrderSequenceInterface::class, $this->orderSequence);
    }

    public function testHasZeroIndexAfterInitialized(): void
    {
        $this->assertSame(0, $this->orderSequence->getIndex());
    }

    public function testIncrementsIndex(): void
    {
        $this->orderSequence->incrementIndex();
        $this->assertSame(1, $this->orderSequence->getIndex());
    }
}
