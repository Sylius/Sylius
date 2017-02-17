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

use Sylius\Component\Resource\Model\TimestampableTrait;
use Webmozart\Assert\Assert;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ExchangeRate implements ExchangeRateInterface
{
    use TimestampableTrait;

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
    protected $sourceCurrency;

    /**
     * @var CurrencyInterface
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
        Assert::nullOrFloat($ratio);

        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCurrency()
    {
        return $this->sourceCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCurrency(CurrencyInterface $currency)
    {
        $this->sourceCurrency = $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetCurrency()
    {
        return $this->targetCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetCurrency(CurrencyInterface $currency)
    {
        $this->targetCurrency = $currency;
    }
}
