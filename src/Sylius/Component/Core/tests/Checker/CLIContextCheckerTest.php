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

namespace Tests\Sylius\Component\Core\Checker;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Checker\CLIContextChecker;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class CLIContextCheckerTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private CLIContextChecker $checker;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->checker = new CLIContextChecker($this->requestStack);
    }

    public function testShouldImplementCommandBasedContextCheckerInterface(): void
    {
        $this->assertInstanceOf(CLIContextCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfProcessIsRunningWithoutCurrentRequest(): void
    {
        $this->requestStack->expects($this->once())->method('getCurrentRequest')->willReturn(null);

        $this->assertTrue($this->checker->isExecutedFromCLI());
    }

    public function testShouldReturnFalseIfProcessIsRunningWithCurrentRequestDefined(): void
    {
        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($this->createMock(Request::class));

        $this->assertFalse($this->checker->isExecutedFromCLI());
    }
}
