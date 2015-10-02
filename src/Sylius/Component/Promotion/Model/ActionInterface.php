<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ActionInterface extends ResourceInterface
{
//    const TYPE_FIXED_DISCOUNT      = 'fixed_discount';
//    const TYPE_PERCENTAGE_DISCOUNT = 'percentage_discount';

    /**
     * @return mixed
     */
    public function getId();

//    /**
//     * @return string
//     */
//    public function getType();
//
//    /**
//     * @param string $type
//     */
//    public function setType($type);

    /**
     * @return array
     */
    public function getConfiguration();

    /**
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);

    /**
     * @return PromotionInterface
     */
    public function getPromotion();

    /**
     * @return ArrayCollection[FilterInterface]
     */
    public function getFilters();

    /**
     * @return ArrayCollection[BenefitInterface]
     */
    public function getBenefits();

    /**
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);

    /**
     * @param BenefitInterface $benefit
     *
     * @return boolean
     */
    public function hasBenefit(BenefitInterface $benefit);

    /**
     * @param BenefitInterface $benefit
     */
    public function addBenefit(BenefitInterface $benefit);

    /**
     * @param BenefitInterface $benefit
     */
    public function removeBenefit(BenefitInterface $benefit);

    /**
     * @param FilterInterface $filter
     *
     * @return boolean
     */
    public function hasFilter(FilterInterface $filter);

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter);

    /**
     * @param FilterInterface $filter
     */
    public function removeFilter(FilterInterface $filter);

}
