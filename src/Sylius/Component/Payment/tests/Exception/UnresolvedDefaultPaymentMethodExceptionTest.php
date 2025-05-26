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

namespace Tests\Sylius\Component\Payment\Exception;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;

final class UnresolvedDefaultPaymentMethodExceptionTest extends TestCase
{
    public function testItIsAnException(): void
    {
        $exception = new UnresolvedDefaultPaymentMethodException();
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testItHasACustomMessage(): void
    {
        $exception = new UnresolvedDefaultPaymentMethodException();
        $this->assertSame('Default payment method could not be resolved!', $exception->getMessage());
    }
}
