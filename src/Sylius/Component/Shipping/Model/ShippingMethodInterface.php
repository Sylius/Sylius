<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Model;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ShippingMethodInterface extends
    CodeAwareInterface,
    ShippingMethodTranslationInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    // Shippables requirement to match given method.
    const CATEGORY_REQUIREMENT_MATCH_NONE = 0;
    const CATEGORY_REQUIREMENT_MATCH_ANY = 1;
    const CATEGORY_REQUIREMENT_MATCH_ALL = 2;

    /**
     * @return null|ShippingCategoryInterface
     */
    public function getCategory();

    /**
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
     * @return int
     */
    public function getCategoryRequirement();

    /**
     * @param int $categoryRequirement
     */
    public function setCategoryRequirement($categoryRequirement);

    /**
     * @return string
     */
    public function getCategoryRequirementLabel();

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
     * @return Collection|RuleInterface[]
     */
    public function getRules();

    /**
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule);

    /**
     * @param RuleInterface $rule
     */
    public function removeRule(RuleInterface $rule);
}
