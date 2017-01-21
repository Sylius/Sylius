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

use Sylius\Component\Resource\Model\ArchivableInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ShippingMethodInterface extends
    ArchivableInterface,
    CodeAwareInterface,
    ShippingMethodTranslationInterface,
    TimestampableInterface,
    ToggleableInterface,
    TranslatableInterface
{
    const CATEGORY_REQUIREMENT_MATCH_NONE = 0;
    const CATEGORY_REQUIREMENT_MATCH_ANY = 1;
    const CATEGORY_REQUIREMENT_MATCH_ALL = 2;

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);

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
     * @return string
     */
    public function getCalculator();

    /**
     * @param string $calculator
     */
    public function setCalculator($calculator);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);
}
