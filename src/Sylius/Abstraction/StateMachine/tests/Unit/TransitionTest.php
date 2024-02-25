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

namespace Tests\Sylius\Abstraction\StateMachine\Unit;

use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\Transition;

final class TransitionTest extends TestCase
{
    public function testItReturnsItsName(): void
    {
        $this->assertSame('name', $this->createTestSubject()->getName());
    }

    public function testItReturnsItsFroms(): void
    {
        $this->assertSame(['from'], $this->createTestSubject()->getFroms());
    }

    public function testItReturnsItsTos(): void
    {
        $this->assertSame(['to'], $this->createTestSubject()->getTos());
    }

    private function createTestSubject(): Transition
    {
        return new Transition('name', ['from'], ['to']);
    }
}
