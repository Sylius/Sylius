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

namespace Tests\Sylius\Component\Core\Positioner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PositionAwareInterface;
use Sylius\Component\Core\Positioner\Positioner;

final class PositionerTest extends TestCase
{
    private MockObject&PositionAwareInterface $positionAwareObject;

    private Positioner $positioner;

    protected function setUp(): void
    {
        $this->positionAwareObject = $this->createMock(PositionAwareInterface::class);
        $this->positioner = new Positioner();
    }

    public function testShouldReturnTrueWhenPositionHasChanged(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);

        $this->assertTrue($this->positioner->hasPositionChanged($this->positionAwareObject, 1));
    }

    public function testShouldReturnFalseWhenPositionHasNotChanged(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);

        $this->assertFalse($this->positioner->hasPositionChanged($this->positionAwareObject, 0));
    }

    public function testShouldUpdatePositionWhenPositionHasChanged(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);
        $this->positionAwareObject->expects($this->once())->method('setPosition')->with(1);

        $this->positioner->updatePosition($this->positionAwareObject, 1, 2);
    }

    public function testShouldNotUpdatePositionWhenPositionHasNotChanged(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);
        $this->positionAwareObject->expects($this->never())->method('setPosition')->with(0);

        $this->positioner->updatePosition($this->positionAwareObject, 0, 2);
    }

    public function testShouldSetNewPositionToMinusOneWhenItIsGreaterThanMaxPosition(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);
        $this->positionAwareObject->expects($this->once())->method('setPosition')->with(-1);

        $this->positioner->updatePosition($this->positionAwareObject, 3, 2);
    }

    public function testShouldSetNewPositionToMinusOneWhenItIsEqualToMaxPosition(): void
    {
        $this->positionAwareObject->expects($this->once())->method('getPosition')->willReturn(0);
        $this->positionAwareObject->expects($this->once())->method('setPosition')->with(-1);

        $this->positioner->updatePosition($this->positionAwareObject, 2, 2);
    }
}
