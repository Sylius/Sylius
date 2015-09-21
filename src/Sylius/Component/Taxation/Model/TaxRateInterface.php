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

use Sylius\Component\Resource\Model\TimestampableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface TaxRateInterface extends TimestampableInterface
{
    /**
     * @return TaxCategoryInterface
     */
    public function getCategory();

    /**
     * @param null|TaxCategoryInterface $category
     */
    public function setCategory(TaxCategoryInterface $category = null);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @return float
     */
    public function getAmountAsPercentage();

    /**
     * @param float $amount
     */
    public function setAmount($amount);

    /**
     * @return Boolean
     */
    public function isIncludedInPrice();

    /**
     * @param Boolean $includedInPrice
     */
    public function setIncludedInPrice($includedInPrice);

    /**
     * @return string
     */
    public function getCalculator();

    /**
     * @param string $calculator
     */
    public function setCalculator($calculator);
}
