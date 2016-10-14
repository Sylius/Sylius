<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxation\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
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
    protected $amount = 0;

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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(TaxCategoryInterface $category = null)
    {
        $this->category = $category;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function getAmountAsPercentage()
    {
        return $this->amount * 100;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludedInPrice()
    {
        return $this->includedInPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludedInPrice($includedInPrice)
    {
        $this->includedInPrice = (bool) $includedInPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalculator()
    {
        return $this->calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function setCalculator($calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return sprintf('%s (%s%%)', $this->name, (float) $this->getAmountAsPercentage());
    }
}
