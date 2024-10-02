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

use Sylius\Bundle\ApiBundle\Attribute\PromotionCodeAware;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

#[PromotionCodeAware]
readonly class GeneratePromotionCoupon implements ReadablePromotionCouponGeneratorInstructionInterface
{
    public function __construct(
        protected string $promotionCode,
        protected ?string $prefix = null,
        protected ?int $codeLength = null,
        protected ?string $suffix = null,
        protected ?int $amount = null,
        protected ?\DateTimeInterface $expiresAt = null,
        protected ?int $usageLimit = null,
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

    public function getCodeLength(): int
    {
        return $this->codeLength;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
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
