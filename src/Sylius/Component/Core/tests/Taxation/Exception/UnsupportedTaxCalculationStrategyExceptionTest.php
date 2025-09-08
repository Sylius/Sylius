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

namespace Tests\Sylius\Component\Core\Taxation\Exception;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;

final class UnsupportedTaxCalculationStrategyExceptionTest extends TestCase
{
    private UnsupportedTaxCalculationStrategyException $exception;

    protected function setUp(): void
    {
        $this->exception = new UnsupportedTaxCalculationStrategyException();
    }

    public function testShouldExtendRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->exception);
    }

    public function testShouldHaveMessage(): void
    {
        $this->assertSame('Unsupported tax calculation strategy!', $this->exception->getMessage());
    }
}
