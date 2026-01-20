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

namespace Tests\Sylius\Component\Promotion\Generator;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

final class PromotionCouponGeneratorInstructionTest extends TestCase
{
    private PromotionCouponGeneratorInstruction $generator;

    protected function setUp(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction();
    }

    public function testShouldImplementPromotionCouponGeneratorInstructionInterface(): void
    {
        $this->assertInstanceOf(ReadablePromotionCouponGeneratorInstructionInterface::class, $this->generator);
    }

    public function testShouldHaveAmountEqualToFiveByDefault(): void
    {
        $this->assertSame(5, $this->generator->getAmount());
    }

    public function testShouldAmountBeMutable(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction(amount: 500);

        $this->assertSame(500, $this->generator->getAmount());
    }

    public function testShouldPrefixBeMutable(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction(prefix: 'PREFIX_');

        $this->assertSame('PREFIX_', $this->generator->getPrefix());
    }

    public function testShouldCodeLengthBeMutable(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction(codeLength: 4);

        $this->assertSame(4, $this->generator->getCodeLength());
    }

    public function testShouldSuffixBeMutable(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction(suffix: '_SUFFIX');

        $this->assertSame('_SUFFIX', $this->generator->getSuffix());
    }

    public function testShouldNotHaveUsageLimitByDefault(): void
    {
        $this->assertNull($this->generator->getUsageLimit());
    }

    public function testShouldUsageLimitBeMutable(): void
    {
        $this->generator = new PromotionCouponGeneratorInstruction(usageLimit: 3);

        $this->assertSame(3, $this->generator->getUsageLimit());
    }
}
