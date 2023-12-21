<?php

namespace Sylius\Component\Promotion\Factory;

use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

interface PromotionCouponGeneratorInstructionFactoryInterface
{
    /** @param array<array-key, mixed> $data */
    public function createFromArray(array $data): ReadablePromotionCouponGeneratorInstructionInterface;
}
