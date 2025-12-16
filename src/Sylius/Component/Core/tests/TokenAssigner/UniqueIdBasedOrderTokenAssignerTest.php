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

namespace Tests\Sylius\Component\Core\TokenAssigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Core\TokenAssigner\UniqueIdBasedOrderTokenAssigner;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;

final class UniqueIdBasedOrderTokenAssignerTest extends TestCase
{
    private const TOKEN_LENGTH = 32;

    private MockObject&RandomnessGeneratorInterface $generator;

    private MockObject&OrderInterface $order;

    private UniqueIdBasedOrderTokenAssigner $assigner;

    protected function setUp(): void
    {
        $this->generator = $this->createMock(RandomnessGeneratorInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->assigner = new UniqueIdBasedOrderTokenAssigner($this->generator, self::TOKEN_LENGTH);
    }

    public function testShouldImplementOrderTokenAssignerInterface(): void
    {
        $this->assertInstanceOf(OrderTokenAssignerInterface::class, $this->assigner);
    }

    public function testShouldAssignTokenValueForOrder(): void
    {
        $this->generator
            ->expects($this->once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('yahboiiiii');
        $this->order->expects($this->once())->method('setTokenValue')->with('yahboiiiii');

        $this->assigner->assignTokenValue($this->order);
    }

    public function testShouldAssignTokenValueIfIsNotSet(): void
    {
        $this->order->expects($this->once())->method('getTokenValue')->willReturn(null);
        $this->generator
            ->expects($this->once())
            ->method('generateUriSafeString')
            ->with(self::TOKEN_LENGTH)
            ->willReturn('yahboiiiii');
        $this->order->expects($this->once())->method('setTokenValue')->with('yahboiiiii');

        $this->assigner->assignTokenValueIfNotSet($this->order);
    }

    public function testShouldDoNothingIfTokenIsAlreadyAssigned(): void
    {
        $this->order->expects($this->once())->method('getTokenValue')->willReturn('yahboiiiii');
        $this->generator
            ->expects($this->never())
            ->method('generateUriSafeString')
            ->with($this->anything());
        $this->order->expects($this->never())->method('setTokenValue')->with($this->anything());

        $this->assigner->assignTokenValueIfNotSet($this->order);
    }
}
