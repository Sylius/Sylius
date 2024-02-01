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

final class PromotionCouponGeneratorInstruction implements ReadablePromotionCouponGeneratorInstructionInterface
{
    public function __construct(
        private ?int $amount = 5,
        private ?string $prefix = null,
        private ?int $codeLength = 6,
        private ?string $suffix = null,
        private ?\DateTimeInterface $expiresAt = null,
        private ?int $usageLimit = null,
    ) {
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getCodeLength(): ?int
    {
        return $this->codeLength;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }
}
