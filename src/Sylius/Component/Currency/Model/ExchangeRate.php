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

namespace Sylius\Component\Currency\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class ExchangeRate implements ExchangeRateInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var float|null
     */
    protected $ratio;

    /**
     * @var CurrencyInterface|null
     */
    protected $sourceCurrency;

    /**
     * @var CurrencyInterface|null
     */
    protected $targetCurrency;

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
    public function getRatio(): ?float
    {
        // It looks like Doctrine is hydrating decimal field as string, force casting to float.
        return is_string($this->ratio) ? (float) $this->ratio : $this->ratio;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setRatio(?float $ratio): void
    {
        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCurrency(): ?CurrencyInterface
    {
        return $this->sourceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCurrency(CurrencyInterface $currency): void
    {
        $this->sourceCurrency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetCurrency(): ?CurrencyInterface
    {
        return $this->targetCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetCurrency(CurrencyInterface $currency): void
    {
        $this->targetCurrency = $currency;
    }
}
