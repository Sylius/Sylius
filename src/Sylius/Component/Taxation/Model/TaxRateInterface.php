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

namespace Sylius\Component\Taxation\Model;

use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface TaxRateInterface extends CodeAwareInterface, TimestampableInterface, ResourceInterface
{
    public function getCategory(): ?TaxCategoryInterface;

    public function setCategory(?TaxCategoryInterface $category): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getAmount(): float;

    public function getAmountAsPercentage(): float;

    public function setAmount(?float $amount): void;

    public function isIncludedInPrice(): bool;

    public function setIncludedInPrice(?bool $includedInPrice): void;

    public function getCalculator(): ?string;

    public function setCalculator(?string $calculator): void;

    public function getLabel(): ?string;

    public function getStartDate(): ?\DateTimeInterface;

    public function setStartDate(?\DateTimeInterface $startDate): void;

    public function getEndDate(): ?\DateTimeInterface;

    public function setEndDate(?\DateTimeInterface $endDate): void;
}
