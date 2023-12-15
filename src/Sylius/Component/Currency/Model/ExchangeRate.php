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

namespace Sylius\Component\Currency\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class ExchangeRate implements ExchangeRateInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var float|null */
    protected $ratio;

    /** @var CurrencyInterface|null */
    protected $sourceCurrency;

    /** @var CurrencyInterface|null */
    protected $targetCurrency;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRatio(): ?float
    {
        /**
         * It looks like Doctrine is hydrating decimal field as string, force casting to float.
         *
         * @var float|string|null $ratio
         */
        $ratio = $this->ratio;

        return is_string($ratio) ? (float) $ratio : $ratio;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setRatio(?float $ratio): void
    {
        $this->ratio = $ratio;
    }

    public function getSourceCurrency(): ?CurrencyInterface
    {
        return $this->sourceCurrency;
    }

    public function setSourceCurrency(CurrencyInterface $currency): void
    {
        $this->sourceCurrency = $currency;
    }

    public function getTargetCurrency(): ?CurrencyInterface
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(CurrencyInterface $currency): void
    {
        $this->targetCurrency = $currency;
    }
}
