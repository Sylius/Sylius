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
 * Tax rate interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface TaxRateInterface
{
    public function getId();
    public function getCategory();
    public function setCategory(TaxCategoryInterface $category = null);
    public function getName();
    public function setName($name);
    public function getAmount();
    public function setAmount($amount);
    public function isIncludedInPrice();
    public function setIncludedInPrice($includedInPrice);
    public function getCalculator();
    public function setCalculator($calculator);
    public function getCreatedAt();
    public function getUpdatedAt();
}
