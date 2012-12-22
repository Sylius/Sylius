<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Model;

/**
 * Tax rate model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class TaxRate implements TaxRateInterface
{
    protected $id;
    protected $category;
    protected $name;
    protected $amount;
    protected $includedInPrice;
    protected $calculator;
    protected $createdAt;
    protected $updatedAt;

    public function __construct()
    {
        $this->amount = 0;
        $this->includedInPrice = false;
        $this->createdAt = new \DateTime('now');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory(TaxCategoryInterface $category = null)
    {
        $this->category = $category;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function isIncludedInPrice()
    {
        return $this->includedInPrice;
    }

    public function setIncludedInPrice($includedInPrice)
    {
        $this->includedInPrice = (Boolean) $includedInPrice;
    }

    public function getCalculator()
    {
        return $this->calculator;
    }

    public function setCalculator($calculator)
    {
        $this->calculator = $calculator;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
