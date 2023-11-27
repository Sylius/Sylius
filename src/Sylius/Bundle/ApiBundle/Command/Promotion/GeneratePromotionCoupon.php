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

namespace Sylius\Bundle\ApiBundle\Command\Promotion;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;

/** @experimental */
class GeneratePromotionCoupon
{
    public function __construct(
        private string $promotionCode,
        private PromotionCouponGeneratorInstructionInterface $promotionCouponGeneratorInstruction,
    ) {
    }

    public function getPromotionCode(): string
    {
        return $this->promotionCode;
    }

    public function getPromotionCouponGeneratorInstruction(): PromotionCouponGeneratorInstructionInterface
    {
        return $this->promotionCouponGeneratorInstruction;
    }
}
