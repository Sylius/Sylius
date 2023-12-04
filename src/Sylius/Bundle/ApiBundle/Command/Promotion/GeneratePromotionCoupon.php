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
class GeneratePromotionCoupon implements PromotionCouponGeneratorInstructionInterface
{
    public function __construct(
        private string $promotionCode,
        private ?string $prefix = null,
        private ?int $codeLength = null,
        private ?string $suffix = null,
        private ?int $amount = null,
        private ?\DateTimeInterface $expiresAt = null,
        private ?int $usageLimit = null,
    ) {
    }

    public function getPromotionCode(): string
    {
        return $this->promotionCode;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getCodeLength(): int
    {
        return $this->codeLength;
    }

    public function setCodeLength(?int $codeLength): void
    {
        $this->codeLength = $codeLength;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function setSuffix(?string $suffix): void
    {
        $this->suffix = $suffix;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    public function setUsageLimit(?int $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }
}
