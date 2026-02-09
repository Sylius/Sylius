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

namespace Tests\Sylius\Component\Promotion\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Factory\PromotionCouponGeneratorInstructionFactory;
use Sylius\Component\Promotion\Factory\PromotionCouponGeneratorInstructionFactoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;

final class PromotionCouponGeneratorInstructionFactoryTest extends TestCase
{
    private PromotionCouponGeneratorInstructionFactory $promotionCouponGeneratorInstructionFactory;

    protected function setUp(): void
    {
        $this->promotionCouponGeneratorInstructionFactory = new PromotionCouponGeneratorInstructionFactory();
    }

    public function testShouldImplementPromotionCouponGeneratorInstructionFactoryInterface(): void
    {
        $this->assertInstanceOf(
            PromotionCouponGeneratorInstructionFactoryInterface::class,
            $this->promotionCouponGeneratorInstructionFactory,
        );
    }

    public function testShouldCreateFromArray(): void
    {
        $now = new \DateTime();
        $data = [
            'expiresAt' => $now,
            'suffix' => 'suffix',
            'prefix' => 'prefix',
            'codeLength' => 10,
            'amount' => 1,
            'usageLimit' => 7,
        ];

        $this->assertEquals(
            new PromotionCouponGeneratorInstruction(1, 'prefix', 10, 'suffix', $now, 7),
            $this->promotionCouponGeneratorInstructionFactory->createFromArray($data),
        );
    }
}
