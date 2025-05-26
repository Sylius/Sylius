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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderSequence;
use Sylius\Component\Core\Model\OrderSequenceInterface;
use Sylius\Component\Order\Model\OrderSequence as BaseOrderSequence;
use Sylius\Resource\Model\VersionedInterface;

final class OrderSequenceTest extends TestCase
{
    private OrderSequence $orderSequence;

    protected function setUp(): void
    {
        $this->orderSequence = new OrderSequence();
    }

    public function testShouldImplementOrderSequenceInterace(): void
    {
        $this->assertInstanceOf(OrderSequenceInterface::class, $this->orderSequence);
    }

    public function testShouldImplementVersionedInterface(): void
    {
        $this->assertInstanceOf(VersionedInterface::class, $this->orderSequence);
    }

    public function testShouldExtendVaseOrderSequence(): void
    {
        $this->assertInstanceOf(BaseOrderSequence::class, $this->orderSequence);
    }

    public function testShouldHaveVersionOneByDefault(): void
    {
        $this->assertSame(1, $this->orderSequence->getVersion());
    }
}
