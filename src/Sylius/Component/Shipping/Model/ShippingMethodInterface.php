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
