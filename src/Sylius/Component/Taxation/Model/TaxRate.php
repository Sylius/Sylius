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

use Sylius\Component\Resource\Model\TimestampableTrait;

class TaxRate implements TaxRateInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $code;

    /** @var TaxCategoryInterface|null */
    protected $category;

    /** @var string|null */
    protected $name;

    /** @var float|null */
    protected $amount = 0.0;

    /** @var bool */
    protected $includedInPrice = false;

    /** @var string|null */
    protected $calculator;

    protected ?\DateTimeInterface $startDate = null;

    protected ?\DateTimeInterface $endDate = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getCategory(): ?TaxCategoryInterface
    {
        return $this->category;
    }

    public function setCategory(?TaxCategoryInterface $category): void
    {
        $this->category = $category;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    public function getAmountAsPercentage(): float
    {
        return $this->amount * 100;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function isIncludedInPrice(): bool
    {
        return $this->includedInPrice;
    }

    public function setIncludedInPrice(?bool $includedInPrice): void
    {
        $this->includedInPrice = $includedInPrice;
    }

    public function getCalculator(): ?string
    {
        return $this->calculator;
    }

    public function setCalculator(?string $calculator): void
    {
        $this->calculator = $calculator;
    }

    public function getLabel(): ?string
    {
        return sprintf('%s (%s%%)', $this->name, $this->getAmountAsPercentage());
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }
}
