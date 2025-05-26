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

namespace Tests\Sylius\Component\Promotion\Exception;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Exception\FailedGenerationException;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

final class FailedGenerationExceptionTest extends TestCase
{
    private MockObject&ReadablePromotionCouponGeneratorInstructionInterface $instruction;

    private \InvalidArgumentException&MockObject $previousException;

    private FailedGenerationException $failedGenerationException;

    protected function setUp(): void
    {
        $this->instruction = $this->createMock(ReadablePromotionCouponGeneratorInstructionInterface::class);
        $this->instruction->expects($this->once())->method('getAmount')->willReturn(17);
        $this->instruction->expects($this->once())->method('getCodeLength')->willReturn(1);
        $this->previousException = $this->createMock(\InvalidArgumentException::class);
        $this->failedGenerationException = new FailedGenerationException(
            $this->instruction,
            0,
            $this->previousException,
        );
    }

    public function testShouldBeInvalidArgumentException(): void
    {
        $this->assertInstanceOf(\InvalidArgumentException::class, $this->failedGenerationException);
    }

    public function testShouldHaveProperMessage(): void
    {
        $this->assertSame(
            'Invalid coupon code length or coupons amount. It is not possible to generate 17 unique coupons with 1 code length',
            $this->failedGenerationException->getMessage(),
        );
    }

    public function testShouldHaveProperPreviousException(): void
    {
        $this->assertSame($this->previousException, $this->failedGenerationException->getPrevious());
    }
}
