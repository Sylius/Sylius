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

namespace Tests\Sylius\Component\Shipping\Exception;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;

final class UnresolvedDefaultShippingMethodExceptionTest extends TestCase
{
    private UnresolvedDefaultShippingMethodException $exception;

    protected function setUp(): void
    {
        $this->exception = new UnresolvedDefaultShippingMethodException();
    }

    public function testShouldBeException(): void
    {
        $this->assertInstanceOf(\Exception::class, $this->exception);
    }

    public function testShouldHaveCustomMessage(): void
    {
        $this->assertSame('Default shipping method could not be resolved!', $this->exception->getMessage());
    }
}
