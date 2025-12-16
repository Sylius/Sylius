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

namespace Tests\Sylius\Component\Shipping\Calculator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;

final class UndefinedShippingMethodExceptionTest extends TestCase
{
    private UndefinedShippingMethodException $undefinedShippingMethodException;

    protected function setUp(): void
    {
        $this->undefinedShippingMethodException = new UndefinedShippingMethodException();
    }

    public function testShouldBeException(): void
    {
        $this->assertInstanceOf(\Exception::class, $this->undefinedShippingMethodException);
    }

    public function testShouldBeInvalidArgumentException(): void
    {
        $this->assertInstanceOf(\InvalidArgumentException::class, $this->undefinedShippingMethodException);
    }
}
