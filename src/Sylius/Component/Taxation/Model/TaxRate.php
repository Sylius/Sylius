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

use Sylius\Component\Resource\Model\TimestampableTrait;

class TaxRate implements TaxRateInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var TaxCategoryInterface
     */
    protected $category;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $amount = 0.0;

    /**
     * @var bool
     */
    protected $includedInPrice = false;

    /**
     * @var string
     */
    protected $calculator;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory(): ?TaxCategoryInterface
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(?TaxCategoryInterface $category): void
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount(): float
    {
        return (float) $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountAsPercentage(): float
    {
        return $this->amount * 100;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludedInPrice(): bool
    {
        return $this->includedInPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludedInPrice(?bool $includedInPrice): void
    {
        $this->includedInPrice = $includedInPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculator(): ?string
    {
        return $this->calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function setCalculator(?string $calculator): void
    {
        $this->calculator = $calculator;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return sprintf('%s (%s%%)', $this->name, $this->getAmountAsPercentage());
    }
}
