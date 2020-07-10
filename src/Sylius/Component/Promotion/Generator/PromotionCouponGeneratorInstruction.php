<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Generator;

final class PromotionCouponGeneratorInstruction implements PromotionCouponGeneratorInstructionInterface
{
    /** @var int */
    private $amount = 5;

    /** @var string|null */
    private $prefix;

    /** @var int */
    private $codeLength = 6;

    /** @var string|null */
    private $suffix;

    /** @var \DateTimeInterface|null */
    private $expiresAt;

    /** @var int|null */
    private $usageLimit;

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function getCodeLength(): ?int
    {
        return $this->codeLength;
    }

    public function setCodeLength(?int $codeLength): void
    {
        $this->codeLength = $codeLength;
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

    public function setUsageLimit(int $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }
}
