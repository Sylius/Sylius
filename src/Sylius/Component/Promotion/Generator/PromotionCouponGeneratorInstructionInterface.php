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

namespace Sylius\Component\Promotion\Generator;

trigger_deprecation(
    'sylius/promotion',
    '1.13',
    'The "%s" interface is deprecated, use "%s" instead.',
    PromotionCouponGeneratorInstructionInterface::class,
    ReadablePromotionCouponGeneratorInstructionInterface::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see ReadablePromotionCouponGeneratorInstructionInterface} instead. */
interface PromotionCouponGeneratorInstructionInterface extends ReadablePromotionCouponGeneratorInstructionInterface
{
    public function setAmount(?int $amount): void;

    public function setPrefix(?string $prefix): void;

    public function setCodeLength(?int $codeLength): void;

    public function setSuffix(?string $suffix): void;

    public function setExpiresAt(?\DateTimeInterface $expiresAt): void;

    public function setUsageLimit(int $usageLimit): void;
}
