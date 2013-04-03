<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Model;

use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Shipping method interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ShippingMethodInterface extends TimestampableInterface
{
    // Shippables requirement to match given method.
    const CATEGORY_REQUIREMENT_MATCH_NONE = 0;
    const CATEGORY_REQUIREMENT_MATCH_ANY  = 1;
    const CATEGORY_REQUIREMENT_MATCH_ALL  = 2;

    /**
     * Get shipping category, if any.
     *
     * @return null|ShippingCategoryInterface
     */
    public function getCategory();

    /**
     * Set shipping category.
     *
     * @param null|ShippingCategoryInterface $category
     */
    public function setCategory(ShippingCategoryInterface $category = null);

    /**
     * Get the one of matching requirements.
     * For example, a method can apply to shipment on 3 different conditions.
     *
     * 1) None of shippables matches the category.
     * 2) At least one of shippables matches the category.
     * 3) All shippables have to match the method category.
     *
     * @return integer
     */
    public function getCategoryRequirement();

    /**
     * Set the requirement.
     *
     * @param integer $categoryRequirement
     */
    public function setCategoryRequirement($categoryRequirement);

    /**
     * Get the human readable label of category requirement.
     *
     * @return string
     */
    public function getCategoryRequirementLabel();

    /**
     * Check whether the shipping method is currently enabled.
     *
     * @return Boolean
     */
    public function isEnabled();

    /**
     * Enable or disable the shipping method.
     *
     * @param Boolean $enabled
     */
    public function setEnabled($enabled);

    /**
     * Get shipping method name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get calculator name assigned for this shipping method.
     *
     * @return string
     */
    public function getCalculator();

    /**
     * Set calculator name assigned for this shipping method.
     *
     * @param string $calculator
     */
    public function setCalculator($calculator);

    /**
     * Get any extra configuration for calculator.
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * Set extra configuration for calculator.
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * Get all rules assigned to this shipping method.
     *
     * @return RuleInterface[]
     */
    public function getRules();

    /**
     * Check if this shipping method already contains the rule?
     *
     * @param RuleInterface $rule
     *
     * @return Boolean
     */
    public function hasRule(RuleInterface $rule);

    /**
     * Adds rule.
     *
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule);

    /**
     * Remove rule.
     *
     * @param RuleInterface $rule
     */
    public function removeRule(RuleInterface $rule);
}
