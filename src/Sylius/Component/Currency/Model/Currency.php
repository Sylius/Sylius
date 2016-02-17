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
use Sylius\Component\Resource\Model\ToggleableTrait;
use Symfony\Component\Intl\Intl;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Currency implements CurrencyInterface
{
    use TimestampableTrait, ToggleableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var float
     */
    protected $exchangeRate;

    /**
     * @var bool
     */
    protected $base = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
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
    public function getName()
    {
        return Intl::getCurrencyBundle()->getCurrencyName($this->code);
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * {@inheritdoc}
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * {@inheritdoc}
     */
    public function setExchangeRate($rate)
    {
        if ($this->isBase()) {
            throw new \LogicException('You cannot change the exchange rate of the base currency!');
        }

        $this->exchangeRate = $rate;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($enabled)
    {
        if ($this->isBase()) {
            throw new \LogicException('You cannot change the enabled status of the base currency!');
        }

        $this->enabled = (bool) $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        if ($this->isBase()) {
            throw new \LogicException('You cannot change the enabled status of the base currency!');
        }

        $this->enabled = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isBase()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function setBase($base)
    {
        $this->base = $base;
    }
}
