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
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;

final class MissingChannelConfigurationExceptionTest extends TestCase
{
    private MissingChannelConfigurationException $exception;

    protected function setUp(): void
    {
        $this->exception = new MissingChannelConfigurationException('Message');
    }

    public function testShouldBeRuntimeException(): void
    {
        $this->assertInstanceOf(\RuntimeException::class, $this->exception);
    }
}
