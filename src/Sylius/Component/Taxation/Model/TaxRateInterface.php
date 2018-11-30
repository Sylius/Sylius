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

    public function getCurrentValue(): TaxValueInterface;

    public function addValue(TaxValueInterface $value): void;

    public function hasValue(TaxValueInterface $value): bool;

    public function removeValue(TaxValueInterface $value): void;

    public function isIncludedInPrice(): bool;

    public function setIncludedInPrice(?bool $includedInPrice): void;

    public function getCalculator(): ?string;

    public function setCalculator(?string $calculator): void;

    public function getLabel(): ?string;
}
