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

interface ReadablePromotionCouponGeneratorInstructionInterface
{
    public function getAmount(): ?int;

    public function getPrefix(): ?string;

    public function getCodeLength(): ?int;

    public function getSuffix(): ?string;

    public function getExpiresAt(): ?\DateTimeInterface;

    public function getUsageLimit(): ?int;
}
