<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Model;

use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ExchangeRate implements ExchangeRateInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var float
     */
    protected $ratio;

    /**
     * @var CurrencyInterface
     */
    protected $baseCurrency;

    /**
     * @var CurrencyInterface
     */
    protected $counterCurrency;

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
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function setRatio($ratio)
    {
        Assert::float($ratio);

        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseCurrency(CurrencyInterface $currency)
    {
        $this->baseCurrency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCounterCurrency()
    {
        return $this->counterCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCounterCurrency(CurrencyInterface $currency)
    {
        $this->counterCurrency = $currency;
    }
}
