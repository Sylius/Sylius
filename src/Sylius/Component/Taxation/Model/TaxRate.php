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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxRate implements TaxRateInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var TaxCategoryInterface
     */
    protected $category;

    /**
     * Name of tax rate.
     *
     * Can be 'EU VAT'.
     *
     * @var string
     */
    protected $name;

    /**
     * @var float
     */
    protected $amount = 0;

    /**
     * @var Boolean
     */
    protected $includedInPrice = false;

    /**
     * @var string
     */
    protected $calculator;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
        return $this->getAmount() * 100;
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
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
