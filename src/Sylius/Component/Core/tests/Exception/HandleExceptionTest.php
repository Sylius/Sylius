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

namespace Tests\Sylius\Component\Core\Exception;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Exception\HandleException;

final class HandleExceptionTest extends TestCase
{
    private HandleException $exception;

    protected function setUp(): void
    {
        $this->exception = new HandleException(HandleException::class, 'request does not have locale code');
    }

    public function testShouldBeRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->exception);
    }

    public function testShouldHaveMessage(): void
    {
        $this->assertSame(
            sprintf('%s was unable to handle this request. request does not have locale code', HandleException::class),
            $this->exception->getMessage(),
        );
    }
}
